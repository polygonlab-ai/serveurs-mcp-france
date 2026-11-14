# Journal des changements

Toutes les évolutions notables de ce dépôt sont décrites ici : à partir de la version 0.1.1, chaque
changement de la liste (entrée, sortie, nouvelle version d'un serveur…) donne une version de correctif,
publiée automatiquement.

Le format suit [Keep a Changelog](https://keepachangelog.com/fr/1.1.0/) et le projet respecte
le [versionnage sémantique](https://semver.org/lang/fr/).

## [0.1.0] - 2026-11-14 - Première publication de la liste

### Ajouté

- Liste des serveurs MCP liés à la France, reprise du registre MCP officiel (registry.modelcontextprotocol.io) : dernière version de chaque serveur actif dont le nom, le titre ou la description contient un mot-clé lié à la France.
- `data/serveurs.json` et `data/serveurs.csv` : pour chaque serveur, nom, titre, description, version, catégorie, dépôt, site, points d'accès distants, paquets d'installation, dates de publication et de mise à jour.
- `data/historique.csv` : entrées, sorties, nouvelles versions, changements de catégorie et serveurs écartés, datés.
- `data/source.json` : adresse lue, date de collecte, pages et entrées lues, empreinte SHA-256 des données normalisées.
- `schema/serveurs.schema.json` : schéma JSON du fichier principal.
- Dix catégories attribuées par mots-clés (`tools/categories.json`), liste d'exclusion tenue à la main (`tools/exclusions.json`), mots-clés modifiables sans toucher au code (`tools/mots-cles.json`).
- `tools/actualiser.php` : outil sans bibliothèque extérieure qui parcourt tout le registre, retient les serveurs liés à la France et réécrit les données et le tableau du README ; collecte enregistrable et rejouable sans réseau.
- Adresses e-mail retirées des descriptions ; serveurs écartés automatiquement si une valeur contient une adresse IPv4, une adresse e-mail hors description, un motif de clé d'API ou un mot interdit.
- Liste factuelle : aucun serveur n'est testé ni recommandé.

[0.1.0]: https://github.com/polygonlab-ai/serveurs-mcp-france/releases/tag/v0.1.0

---
— Conçu et développé par PolygonLab · polygonlab.ai · Licence MIT
