<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Tri des serveurs retenus par la collecte, avant publication.
 *
 *   1. liste d'exclusion : serveur retiré (choix éditorial) ;
 *   2. adresse e-mail dans la description : remplacée par « [adresse retirée] », cas signalé ;
 *   3. serveur écarté automatiquement, sans bloquer les autres, si l'une de ses valeurs publiées (nom, titre,
 *      description, version, adresses, paquets) contient un mot interdit (option --mots-interdits), une adresse
 *      IPv4, une adresse e-mail (hors description), un motif de secret (clé d'API, jeton) ou un texte entre
 *      doubles accolades.
 *
 * Un serveur écarté est cité par son nom, sauf si le nom lui-même est en cause : il devient alors
 * « [nom retiré xxxxxxxx] » (début de l'empreinte SHA-256 du nom), pour que rien de refusé ne soit publié.
 *
 * @phpstan-type Discarded array{server: Server, name: string, reason: string}
 * @phpstan-type Excluded array{server: Server, reason: string}
 */
final class Screening
{
    public const EMAIL_REPLACEMENT = '[adresse retirée]';

    public const EMAIL = '/[A-Za-z0-9._%+-]+@[A-Za-z0-9-]+(?:\.[A-Za-z0-9-]+)*\.[A-Za-z]{2,}/';

    public const IPV4 = '/(?<![\d.])((?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)(?:\.(?:25[0-5]|2[0-4]\d|1\d\d|[1-9]?\d)){3})(?!\.?\d)/';

    private const TEMPLATE = '/(?<!\$)\{\{[^{}\n]*\}\}/';

    /** Motifs de secrets (clés d'API, jetons). */
    private const SECRETS = [
        '/\bgh[pousr]_[A-Za-z0-9]{20,}/',
        '/\bgithub_pat_[A-Za-z0-9_]{20,}/',
        '/(?<![A-Za-z0-9_-])sk-[A-Za-z0-9_-]{20,}/',
        '/\b(?:AKIA|ASIA)[0-9A-Z]{16}\b/',
        '/-{5}BEGIN/',
        '/\bxox[abposr]-[A-Za-z0-9-]{10,}/',
        '/\bxapp-[A-Za-z0-9-]{10,}/',
        '/\bSG\.[A-Za-z0-9_-]{16,}\.[A-Za-z0-9_-]{16,}/',
        '/\bAIza[0-9A-Za-z_-]{35}/',
        '/\b(?:sk|rk|pk)_(?:live|test)_[A-Za-z0-9]{16,}/',
        '/\bwhsec_[A-Za-z0-9]{20,}/',
        '/\bglpat-[A-Za-z0-9_-]{20,}/',
        '/\bnpm_[A-Za-z0-9]{36}\b/',
        '/\beyJ[A-Za-z0-9_-]{10,}\.eyJ[A-Za-z0-9_-]{10,}\.[A-Za-z0-9_-]{10,}/',
        '~[a-z][a-z0-9+.-]*://[^/\s:@"\'<>]+:[^/\s@"\'<>]+@~i',
        '/\b(?:password|passwd|pwd|mot_de_passe|secret|api_?key|access_?key|token)\b["\']?\s*(?:=>|=|:)\s*["\']([^"\'\s]{12,})["\']/i',
    ];

    public function __construct(
        private readonly Exclusions $exclusions,
        private readonly ForbiddenWords $forbiddenWords,
    ) {}

    /**
     * @param list<Server> $servers
     *
     * @return array{kept: list<Server>, discarded: list<Discarded>, excluded: list<Excluded>, emails: list<string>}
     */
    public function screen(array $servers): array
    {
        $kept = [];
        $discarded = [];
        $excluded = [];
        $emails = [];
        foreach ($servers as $server) {
            $exclusion = $this->exclusions->reason($server->name);
            if ($exclusion !== null) {
                $excluded[] = ['server' => $server, 'reason' => $exclusion];
                continue;
            }
            if ($server->description !== null) {
                $description = (string) preg_replace(self::EMAIL, self::EMAIL_REPLACEMENT, $server->description);
                if ($description !== $server->description) {
                    $emails[] = $server->name;
                    $server = $server->withDescription($description);
                }
            }
            $reason = null;
            foreach ($server->texts() as $field => $text) {
                $reason = $this->problem($text, $field !== 'description');
                if ($reason !== null) {
                    $reason .= ' (champ « ' . $field . ' »)';
                    break;
                }
            }
            if ($reason === null) {
                $kept[] = $server;
                continue;
            }
            $discarded[] = ['server' => $server, 'name' => $this->displayName($server->name), 'reason' => $reason];
        }

        return ['kept' => $kept, 'discarded' => $discarded, 'excluded' => $excluded, 'emails' => $emails];
    }

    /**
     * Nom publiable d'un serveur : son nom, ou « [nom retiré xxxxxxxx] » si le nom lui-même est refusé.
     */
    public function displayName(string $name): string
    {
        return $this->problem($name, true) === null ? $name : '[nom retiré ' . substr(hash('sha256', $name), 0, 8) . ']';
    }

    /**
     * Motif de refus d'une valeur, ou null si elle est publiable.
     */
    private function problem(string $text, bool $checkEmail): ?string
    {
        if ($this->forbiddenWords->found($text)) {
            return 'mot interdit';
        }
        if (preg_match(self::IPV4, $text) === 1) {
            return 'adresse IPv4';
        }
        if ($checkEmail && preg_match(self::EMAIL, $text) === 1) {
            return 'adresse e-mail';
        }
        foreach (self::SECRETS as $pattern) {
            if (preg_match($pattern, $text) === 1) {
                return 'motif de clé d\'API ou de jeton';
            }
        }
        if (preg_match(self::TEMPLATE, $text) === 1) {
            return 'texte entre doubles accolades';
        }

        return null;
    }
}
