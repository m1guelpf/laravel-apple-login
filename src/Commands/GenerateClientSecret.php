<?php

namespace M1guelpf\LoginWithApple\Commands;

use Illuminate\Console\Command;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Ecdsa\MultibyteStringConverter;
use Lcobucci\JWT\Signer\Ecdsa\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Ecdsa\SignatureConverter;

class GenerateClientSecret extends Command
{
    public $signature = 'apple:secret {key_path : Relative path to the private key}
        {--T|team_id= : The ID of the team that owns the key, found on the top-right corner of your Apple Developer page (under your name)}
        {--C|client_id= : The identifier of the Service ID the key belongs to}
        {--K|key_id= : The ID of the private key, found on the key page}';

    public $description = 'Generate a Client Secret from your Sign In with Apple private key';

    public function handle()
    {
        $keyPath = getcwd().DIRECTORY_SEPARATOR.$this->argument('key_path');

        if (! file_exists($keyPath)) {
            $this->error('Key file not found at path.');
            return 1;
        }

        if (! $teamId = $this->option('team_id')) {
            $teamId = $this->ask('Team ID (found on the top-right corner of your Apple Developer page, under your name): ');
        }

        if (! $clientId = $this->option('client_id')) {
            $clientId = $this->ask('Client ID (the identifier of the Service ID the key belongs to): ');
        }

        if (! $keyId = $this->option('client_id')) {
            $keyId = $this->ask('Key ID (the ID of the private key, found on the key page): ');
        }

        $key = with(Configuration::forSymmetricSigner(
            new Sha256(new MultibyteStringConverter),
            InMemory::file($keyPath),
        ), fn($config) => $config->builder()
            ->issuedBy($teamId)
            ->issuedAt(now()->setMilliseconds(0)->toDateTimeImmutable())
            ->expiresAt(now()->addYear()->setMilliseconds(0)->toDateTimeImmutable())
            ->permittedFor('https://appleid.apple.com')
            ->relatedTo($clientId)
            ->withHeader('kid', $keyId)
            ->getToken($config->signer(), $config->signingKey())
            ->toString()
        );

        $this->comment('Your Client Secret is:');
        $this->line($key);
    }
}
