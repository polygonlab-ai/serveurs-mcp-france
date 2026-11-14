<?php

declare(strict_types=1);

namespace PolygonLab\ServeursMcpFrance\Outils;

/**
 * Un serveur MCP tel que repris du registre officiel : faits publiés par le registre, nettoyés, rien d'autre.
 *
 * @phpstan-type Remote array{type: string, url: string}
 * @phpstan-type Package array{registre: string, identifiant: string}
 * @phpstan-type ServerArray array{nom: string, titre: string|null, description: string|null, version: string, depot: string|null, site: string|null, distant: list<Remote>, paquets: list<Package>, publie_le: string|null, mis_a_jour_le: string|null}
 */
final class Server
{
    /** Champs comparés d'une version de la liste à l'autre, dans l'ordre du fichier publié. */
    public const FIELDS = ['nom', 'titre', 'description', 'version', 'depot', 'site', 'distant', 'paquets', 'publie_le', 'mis_a_jour_le'];

    /**
     * @param list<Remote> $remotes
     * @param list<Package> $packages
     */
    public function __construct(
        public readonly string $name,
        public readonly ?string $title,
        public readonly ?string $description,
        public readonly string $version,
        public readonly ?string $repository,
        public readonly ?string $website,
        public readonly array $remotes,
        public readonly array $packages,
        public readonly ?string $publishedOn,
        public readonly ?string $updatedOn,
    ) {}

    /**
     * Même serveur, autre description (adresse e-mail retirée).
     */
    public function withDescription(?string $description): self
    {
        return new self($this->name, $this->title, $description, $this->version, $this->repository, $this->website, $this->remotes, $this->packages, $this->publishedOn, $this->updatedOn);
    }

    /**
     * Texte simplifié du nom, du titre et de la description, pour les mots-clés et les catégories (sans la mention
     * « [adresse retirée] », qui ne dit rien du serveur).
     */
    public function searchText(): string
    {
        $description = str_replace(Screening::EMAIL_REPLACEMENT, ' ', $this->description ?? '');

        return Text::simplify($this->name . ' ' . ($this->title ?? '') . ' ' . $description);
    }

    /**
     * Valeurs textuelles publiées, par champ (listes aplaties), pour les contrôles de Screening.
     *
     * @return array<string, string>
     */
    public function texts(): array
    {
        $texts = ['nom' => $this->name, 'version' => $this->version];
        foreach (['titre' => $this->title, 'description' => $this->description, 'depot' => $this->repository, 'site' => $this->website] as $field => $value) {
            if ($value !== null) {
                $texts[$field] = $value;
            }
        }
        if ($this->remotes !== []) {
            $texts['distant'] = DataFiles::flattenRemotes($this->remotes);
        }
        if ($this->packages !== []) {
            $texts['paquets'] = DataFiles::flattenPackages($this->packages);
        }

        return $texts;
    }

    /**
     * @return ServerArray
     */
    public function toArray(): array
    {
        return [
            'nom' => $this->name,
            'titre' => $this->title,
            'description' => $this->description,
            'version' => $this->version,
            'depot' => $this->repository,
            'site' => $this->website,
            'distant' => $this->remotes,
            'paquets' => $this->packages,
            'publie_le' => $this->publishedOn,
            'mis_a_jour_le' => $this->updatedOn,
        ];
    }

    /**
     * Serveur relu d'un fichier de l'outil (registre normalisé ou data/serveurs.json).
     *
     * @param array<mixed> $data
     *
     * @throws SourceError
     */
    public static function fromArray(array $data, string $label): self
    {
        $name = $data['nom'] ?? null;
        $version = $data['version'] ?? null;
        if (!\is_string($name) || $name === '' || !\is_string($version) || $version === '') {
            throw new SourceError($label . ' : serveur sans « nom » ou sans « version ».');
        }
        $optional = [];
        foreach (['titre', 'description', 'depot', 'site', 'publie_le', 'mis_a_jour_le'] as $field) {
            $value = $data[$field] ?? null;
            if ($value !== null && !\is_string($value)) {
                throw new SourceError($label . ' : « ' . $field . ' » de « ' . $name . ' » : texte ou null attendu.');
            }
            $optional[$field] = $value;
        }
        $remotes = [];
        foreach (self::list($data['distant'] ?? [], $label, $name, 'distant') as $remote) {
            if (!\is_string($remote['type'] ?? null) || !\is_string($remote['url'] ?? null)) {
                throw new SourceError($label . ' : « distant » de « ' . $name . ' » : objets {type, url} attendus.');
            }
            $remotes[] = ['type' => $remote['type'], 'url' => $remote['url']];
        }
        $packages = [];
        foreach (self::list($data['paquets'] ?? [], $label, $name, 'paquets') as $package) {
            if (!\is_string($package['registre'] ?? null) || !\is_string($package['identifiant'] ?? null)) {
                throw new SourceError($label . ' : « paquets » de « ' . $name . ' » : objets {registre, identifiant} attendus.');
            }
            $packages[] = ['registre' => $package['registre'], 'identifiant' => $package['identifiant']];
        }

        return new self(
            $name,
            $optional['titre'],
            $optional['description'],
            $version,
            $optional['depot'],
            $optional['site'],
            $remotes,
            $packages,
            $optional['publie_le'],
            $optional['mis_a_jour_le'],
        );
    }

    /**
     * @return list<array<mixed>>
     *
     * @throws SourceError
     */
    private static function list(mixed $value, string $label, string $name, string $field): array
    {
        if (!\is_array($value) || !array_is_list($value)) {
            throw new SourceError($label . ' : « ' . $field . ' » de « ' . $name . ' » : liste attendue.');
        }
        $items = [];
        foreach ($value as $item) {
            if (!\is_array($item)) {
                throw new SourceError($label . ' : « ' . $field . ' » de « ' . $name . ' » : objets attendus.');
            }
            $items[] = $item;
        }

        return $items;
    }
}
