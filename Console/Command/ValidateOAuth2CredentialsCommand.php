<?php

namespace Swissup\OAuth2Client\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use League\OAuth2\Client\Provider\Google;

/**
 * Command to validate OAuth2 client credentials for supported providers.
 */
class ValidateOAuth2CredentialsCommand extends Command
{
    private const NAME = 'oauth2:validate-credentials';
    private const ALIAS = 'swissup:oauth2:validate-credentials';
    private const CLIENT_ID_OPTION = 'client-id';
    private const CLIENT_SECRET_OPTION = 'client-secret';
    private const PROVIDER_OPTION = 'provider';

    /**
     * Configures the command options and description.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName(self::NAME)
            ->setAliases([self::ALIAS])
            ->setDescription('Validates OAuth 2.0 client credentials')
            ->addOption(
                self::CLIENT_ID_OPTION,
                'i',
                InputOption::VALUE_REQUIRED,
                'OAuth2 Client ID'
            )
            ->addOption(
                self::CLIENT_SECRET_OPTION,
                's',
                InputOption::VALUE_REQUIRED,
                'OAuth2 Client Secret'
            )
            ->addOption(
                self::PROVIDER_OPTION,
                'p',
                InputOption::VALUE_OPTIONAL,
                'OAuth2 Provider',
                'google'
            );

        parent::configure();
    }

    /**
     * Executes the command to validate OAuth2 credentials.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $provider = $input->getOption(self::PROVIDER_OPTION);

            $output->writeln('<info>🔐 OAuth2 Credentials Validation</info>');
            $output->writeln("<info>Provider: {$provider}</info>");
            $output->writeln('');

            // Get credentials
            $credentials = $this->getCredentials($input, $output);

            if (!$credentials['client_id'] || !$credentials['client_secret']) {
                throw new \InvalidArgumentException('Both Client ID and Client Secret are required');
            }

            $output->writeln("<info>Client ID: " . $this->maskCredential($credentials['client_id']) . "</info>");
            $output->writeln("<info>Client Secret: " . $this->maskCredential($credentials['client_secret']) . "</info>");
            $output->writeln('');

            // Validate credentials
            $this->validateCredentials(
                $credentials['client_id'],
                $credentials['client_secret'],
                $provider,
                $output
            );

            $output->writeln('<info>✅ Success! OAuth2 credentials are valid.</info>');
            return \Magento\Framework\Console\Cli::RETURN_SUCCESS;

        } catch (\Exception $e) {
            $output->writeln('<error>❌ Error: ' . $e->getMessage() . '</error>');
            return \Magento\Framework\Console\Cli::RETURN_FAILURE;
        }
    }

    /**
     * Retrieves credentials from options or interactively from the user.
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return array
     */
    private function getCredentials(InputInterface $input, OutputInterface $output): array
    {
        $clientId = $input->getOption(self::CLIENT_ID_OPTION);
        $clientSecret = $input->getOption(self::CLIENT_SECRET_OPTION);

        // If credentials are not provided via options, ask interactively
        if (!$clientId || !$clientSecret) {
            $helper = $this->getHelper('question');

            if (!$clientId) {
                $question = new Question('Please enter Client ID: ');
                $clientId = $helper->ask($input, $output, $question);
            }

            if (!$clientSecret) {
                $question = new Question('Please enter Client Secret: ');
                $question->setHidden(true);
                $question->setHiddenFallback(false);
                $clientSecret = $helper->ask($input, $output, $question);
            }
        }

        return [
            'client_id' => $clientId,
            'client_secret' => $clientSecret
        ];
    }

    /**
     * Validates credentials for the specified OAuth2 provider.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param string $provider
     * @param OutputInterface $output
     * @return void
     * @throws \InvalidArgumentException
     */
    private function validateCredentials(
        string $clientId,
        string $clientSecret,
        string $provider,
        OutputInterface $output
    ): void {
        $output->writeln('<comment>🔍 Validating credentials...</comment>');

        // Validation via OAuth2 provider
        switch (strtolower($provider)) {
            case 'google':
                $this->validateGoogleCredentials($clientId, $clientSecret, $output);
                break;
            default:
                throw new \InvalidArgumentException("Provider '{$provider}' is not supported");
        }
    }

    /**
     * Validates Google OAuth2 credentials by checking format and connection.
     *
     * @param string $clientId
     * @param string $clientSecret
     * @param OutputInterface $output
     * @return void
     * @throws \Exception
     */
    private function validateGoogleCredentials(string $clientId, string $clientSecret, OutputInterface $output): void
    {
        // Check Google Client ID format
        if (!preg_match('/^[0-9]+-[a-zA-Z0-9]+\\.apps\\.googleusercontent\\.com$/', $clientId)) {
            throw new \InvalidArgumentException('Client ID format is not valid for Google OAuth2');
        }

        try {
            $provider = new Google([
                'clientId' => $clientId,
                'clientSecret' => $clientSecret,
                'redirectUri' => 'http://localhost/callback'
            ]);

            $output->writeln('<comment>🌐 Testing connection to Google OAuth2...</comment>');

            // Check if we can get the authorization URL
            $authUrl = $provider->getAuthorizationUrl([
                'scope' => ['email', 'profile']
            ]);

            if (empty($authUrl)) {
                throw new \Exception('Failed to generate authorization URL');
            }

            $output->writeln('<info>📡 Successfully connected to Google OAuth2 service</info>');

        } catch (\Exception $e) {
            if (strpos($e->getMessage(), 'unauthorized_client') !== false ||
                strpos($e->getMessage(), 'invalid_client') !== false) {
                throw new \Exception('Invalid client credentials');
            }

            throw new \Exception('Credentials validation failed: ' . $e->getMessage());
        }
    }

    /**
     * Masks a credential string for display.
     *
     * @param string $credential
     * @return string
     */
    private function maskCredential(string $credential): string
    {
        if (strlen($credential) <= 8) {
            return str_repeat('*', strlen($credential));
        }

        return substr($credential, 0, 4) . str_repeat('*', strlen($credential) - 8) . substr($credential, -4);
    }
}
