```
       ▄▄▀▀▄▄        ____       _                         _          _
    ▄▀▀  ▄▄  ▀▀▄    |  _ \ ___ | |_   _  __ _  ___  _ __ | |    __ _| |__
    █  ▄▀  ▀▄  █    | |_) / _ \| | | | |/ _` |/ _ \| '_ \| |   / _` | '_ \
    █  ▀▄  ▄▀  █    |  __/ (_) | | |_| | (_| | (_) | | | | |__| (_| | |_) |
    ▀▄▄  ▀▀  ▄▄▀    |_|   \___/|_|\__, |\__, |\___/|_| |_|_____\__,_|_.__/
       ▀▀▄▄▀▀                     |___/ |___/
                                   polygonlab.ai
```

# serveurs-mcp-france

> Liste des serveurs MCP liés à la France, reprise du registre MCP officiel, en JSON et CSV, mise à jour chaque semaine.

[![CI](https://github.com/polygonlab-ai/serveurs-mcp-france/actions/workflows/ci.yml/badge.svg)](https://github.com/polygonlab-ai/serveurs-mcp-france/actions/workflows/ci.yml)
[![Version](https://img.shields.io/github/v/release/polygonlab-ai/serveurs-mcp-france)](https://github.com/polygonlab-ai/serveurs-mcp-france/releases)
[![Licence](https://img.shields.io/badge/licence-MIT-blue)](LICENSE)

**Liste factuelle reprise du registre officiel : PolygonLab ne teste ni ne recommande ces serveurs.**

## Pourquoi

Un serveur MCP (Model Context Protocol) donne à un assistant d'IA l'accès à un service : une base de données
publique, une API, un logiciel métier. Le registre MCP officiel en recense des dizaines de milliers, sans moyen
simple de trouver ceux qui concernent la France : entreprises et SIREN, données publiques, emploi, santé,
fiscalité, transport.

Ce dépôt en tient une liste **datée, classée et historisée**, dans des formats faciles à réutiliser : un fichier
JSON avec son schéma, un fichier CSV qui s'ouvre directement dans Excel, et l'historique des entrées, sorties et
nouvelles versions. Il ne reprend que les faits publiés dans le registre : rien n'est testé, noté ni recommandé.
Le registre est lu **chaque lundi** ; une nouvelle version est publiée dès que la liste change.

## Fonctionnalités

- Serveurs actifs du registre MCP officiel dont le nom, le titre ou la description évoque la France.
- Dix catégories (entreprises, emploi, santé, juridique, fiscalité, transport, géographie, données publiques, commerce, autre).
- Pour chaque serveur : version, dépôt de code, site, points d'accès distants, paquets d'installation, dates.
- Historique daté : entrées, sorties, nouvelles versions, changements de catégorie, serveurs écartés.
- Traçabilité de la source : adresse lue, date, pages et entrées lues, empreinte SHA-256.
- Schéma JSON du fichier principal, vérifié à chaque publication.
- Outil d'actualisation en PHP, utilisable par n'importe qui pour refaire la liste soi-même.

## La liste

Tableau généré à chaque actualisation, à partir de `data/serveurs.json`.

<!-- liste-serveurs:debut -->
Liste du 14/11/2026 : 356 serveurs, 10 catégories. Fichier complet : [`data/serveurs.json`](data/serveurs.json).

### Entreprises et registres (55)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Siren**<br>`ai.mysiren/siren` | Deterministic visual marketing engine. Your agent plans, renders, and posts on-brand campaigns. | 1.1.0 | [dépôt](https://github.com/Hexahedral-Inc/siren-mcp) |
| **profils.ai French company registry**<br>`ai.profils/registry` | Every active French company by SIREN: search, full fiche, and filed PDFs paid per fetch by agents. | 1.0.0 | [site](https://profils.ai/agents) |
| **Vérif Entreprise FR**<br>`app.railway.up.api-production-24833/verif-entreprise-fr` | French company KYB (SIREN): status, insolvency, VAT, BODACC monitoring alerts. Free preview; x402. | 1.3.0 | [site](https://api-production-24833.up.railway.app) |
| **TrustyData**<br>`app.trustydata/trustydata` | French address quality, geocoding, routing, company lookup &amp; catchment stats (BAN, INSEE, OSM). | 1.6.0 | [dépôt](https://github.com/htristram/trustydata-mcp-server) · [site](https://trustydata.fr) |
| **304NotModified**<br>`com.304notfound/304notmodified` | Sourced answers on FR/EU rules + French company search (SIREN, NAF) for agents, quoted and budgeted | 0.4.0 | [site](https://304notfound.com) |
| **VerifyDesk**<br>`com.agenttoolworks/verifydesk` | Verify French and UK companies, EU VAT and IBANs; screen OFAC, UN, EU and UK sanctions lists. | 1.2.0 | [dépôt](https://github.com/agenttoolworks/mcp-servers) · [site](https://agenttoolworks.com/servers/verifydesk) |
| **Arnakoff**<br>`com.arnakoff/api` | Signaux anti-arnaque sourcés sur un site, un e-mail, un numéro, un IBAN ou un SIREN | 1.0.0 | [dépôt](https://github.com/arnakoff/api) |
| **Corporary**<br>`com.corporary/corporary` | French company data: SIREN search, legal profile, financials, changes, groups, filings, scores. | 1.0.0 | [site](https://corporary.com/en/api) |
| **CréaCompare**<br>`com.creacompare/creacompare` | Formes juridiques, frais légaux et offres de création d'entreprise en France, par CréaCompare. | 1.0.0 | [site](https://creacompare.com/serveur-mcp) |
| **Dataroia Data Trust**<br>`com.dataroia/data-trust` | Vérification B2B française : SIRET, SIRENE, dirigeants RNE, santé d'entreprise. Hébergé en France. | 1.0.0 | [site](https://dataroia.com/api.html) |
| **Fondatoria**<br>`com.fondatoria/sites-vitrines` | Créer son entreprise en France (statut, nom, aides) et son site vitrine : refonte, création, import. | 1.4.0 | [site](https://fondatoria.com) |
| **Fuentio: UK, France &amp; Spain company data from official registries**<br>`com.fuentio/company-data` | Official UK, France and Spain company checks for AI agents | 1.1.0 | [dépôt](https://github.com/THEMAGNET-NICHEP-Lab/fuentio-mcp) · [site](https://fuentio.com) |
| **MaFactureOK**<br>`com.mafactureok/mafactureok` | Vérifie une facture électronique (Factur-X, UBL, CII), un SIREN/SIRET, une TVA ; explique un rejet. | 1.1.0 | [site](https://mafactureok.com/serveur-mcp) |
| `com.mambabuilt/mcp-new-company-registrations-feed` | Newly registered companies from UK, Norway, Finland, and France registers, one row per company. | 1.0.1 | [dépôt](https://github.com/mambalabsdev/mcp-new-company-registrations-feed) |
| **Sirenic**<br>`eu.sirenic/sirenic` | Official French and European company data for AI agents, paid per call (x402) or by API key. | 1.1.0 | [site](https://api.sirenic.eu/) |
| **auregistre**<br>`fr.auregistre/auregistre` | French companies: dated changes and insolvencies (BODACC), filed accounts by year (INPI). No key. | 0.1.1 | [site](https://auregistre.fr/api) |
| **LegalOne Entreprises**<br>`fr.legalone/entreprises` | French company data from public registers (Sirene, RNE, BODACC) and French legal notices. | 0.1.1 | [dépôt](https://github.com/legalone-fr/entreprises) · [site](https://legalone.fr/developers) |
| **Nogogo AI - French Company Reliability**<br>`fr.nogogo/nogogo-ai` | Check one French company by name or SIREN and get its Nogogo A-E reliability grade for free. | 1.1.0 | [site](https://nogogo.fr/integration) |
| **Origami Entreprises**<br>`fr.origami-entreprises/origami` | French company data: financials, dirigeants, BODACC, INPI filings, PEP checks, alerts. | 1.0.0 | [site](https://origami-entreprises.fr/mcp/) |
| **PublicOffre — marchés publics français**<br>`fr.publicoffre/marches-publics` | French public procurement: open tenders, who wins contracts, company references, local finances. | 3.31.0 | [site](https://www.publicoffre.fr/) |
| **Registers**<br>`fr.registers/registers` | French public registers: companies (SIRENE, BODACC) and real estate (cadastre, DVF, DPE, risks). | 1.0.0 | [site](https://registers.fr) |
| **socs.fr, la commande publique française**<br>`fr.socs/commande-publique` | French public procurement: open tenders from BOAMP, public buyers, and contracts won by a company. | 0.2.0 | [site](https://socs.fr/agents) |
| **RealGeeks Company Registry**<br>`host.beacon.worker.realgeeks-company-api/company-registry` | Official company registers for France, Norway, Finland and US SEC: search by name or look up by ID. | 1.1.0 | [site](https://realgeeks-company-api.worker.beacon.host) |
| **RNCP / RS — certifications professionnelles (France compétences)**<br>`io.github.ALDEBERTArnaud/mcp-rncp` | French RNCP/RS certification registers: search, sheets, blocs, validity, SIRET habilitation. | 0.1.4 | [dépôt](https://github.com/ALDEBERTArnaud/mcp-rncp) |
| **Merx — European Business Data**<br>`io.github.BricePourLe13/merx` | European business data — French company check, EU VAT validation, legal search. | 1.0.0 | [dépôt](https://github.com/BricePourLe13/merxprotocol) |
| **fr-legal-kit**<br>`io.github.CartonPliant/fr-legal-kit` | French legal helpers for agents: e-invoice, L441-10, SIRET/IBAN. $0.01 USDC x402 | 1.3.0 | [dépôt](https://github.com/CartonPliant/fr-legal-kit) · [site](https://fr-legal-kit.monnet-yanis1.workers.dev) |
| `io.github.CloudCorpRecords/firmproof` | Official company checks for the UK, France and Spain: lookup, verify, screen, monitor. | 0.1.0 | [dépôt](https://github.com/CloudCorpRecords/firmproof) |
| **France Care, accès aux soins et prévention**<br>`io.github.France-Care/france-care` | Accès aux soins en France, qui saisir pour une démarche de santé, prévention en entreprise. | 1.2.0 | [dépôt](https://github.com/France-Care/claude-plugin-france-care) · [site](https://mcp.france-care.fr/plugin) |
| **France Company Check**<br>`io.github.JusticeUA/france-company-check` | France company checks by SIREN: status, insolvency, LEI, sanctions, public contracts (DECP and TED). | 0.1.2 | [site](https://apify.com/kyb-data/france-company-check) |
| **Sentinelle créances FR**<br>`io.github.Marcoco27/sentinelle-creances` | French insolvency notices (BODACC) per company, with the creditor claim filing deadline. | 0.1.1 | [dépôt](https://github.com/Marcoco27/sentinelle-creances-mcp) · [site](https://creances.amacunia.fr/) |
| **Agent Data Tools: Company and VAT check**<br>`io.github.Nutshell2000/agent-data-tools-company-check` | 8 tools: EU VAT (VIES), Nordic and French company registers, LEI and OFAC sanctions checks. | 1.177.0 | [dépôt](https://github.com/Nutshell2000/agent-data-tools) · [site](https://agent-data-tools.revmesh2074.workers.dev/) |
| **France Company Search (SIREN / SIRET)**<br>`io.github.TheCompanyAtlas/france-siren-company` | French company search by name and SIREN, with SIRET, legal category and status. | 1.0.0 | [dépôt](https://github.com/TheCompanyAtlas/france-siren-company-mcp) · [site](https://thecompanyatlas.com) |
| **French Due Diligence**<br>`io.github.Zansuken/french-due-diligence` | Keyless French company checks (Sirene, BODACC, VIES, sanctions), DPE compliance, product recalls | 0.5.0 |  |
| **Klaren — French Companies Directory**<br>`io.github.ccamborde/klaren-mcp` | Search French companies, directors and associations (INPI, INSEE, RNA). | 0.1.2 | [dépôt](https://github.com/ccamborde/klaren.fr) |
| **Resolve402**<br>`io.github.charleskremer-hub/resolve402` | French directors, NACE company search (FR, NO, FI), KYB checks, contacts, hiring signals, x402. | 0.5.0 | [site](https://resolve402.dev) |
| **French Companies Registry Data**<br>`io.github.corent1robert/french-companies-data` | French company data by SIREN or search: address, NAF code, directors, VAT and financials | 1.0.0 |  |
| **Welcome to the Jungle Jobs Scraper (Apify) - public WTTJ board**<br>`io.github.datadmon/welcome-to-the-jungle-jobs-scraper` | WTTJ France-focused public board jobs as JSON: full JD, company, only-new feed. $0.80/1k | 1.0.1 | [dépôt](https://github.com/datadmon/apify-data-actors-examples) · [site](https://apify.com/data_daemon/welcome-to-the-jungle-jobs-scraper) |
| **INPI**<br>`io.github.mcp-dir/inpi-mcp` | Brazilian INPI trademark &amp; patent lookup by name, process number or holder (CPF/CNPJ). | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi-mcp) |
| **INPI: Marcas**<br>`io.github.mcp-dir/inpi_marcas-mcp` | INPI: Marcas, official-source lookup. Platform-hosted, pay per query with prepaid credit. | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi_marcas-mcp) |
| **INPI: Processo de Registro de Marca (Completa)**<br>`io.github.mcp-dir/inpi_marcas_processo-mcp` | INPI: Court Case de Registro de Marca (Completa), official-source lookup. Platform-hosted, pay per q | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi_marcas_processo-mcp) |
| **INPI: Processo de Registro de Marca (Resumida)**<br>`io.github.mcp-dir/inpi_marcas_processo_resumida-mcp` | INPI: Court Case de Registro de Marca (Resumida), official-source lookup. Platform-hosted, pay per q | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi_marcas_processo_resumida-mcp) |
| **INPI Marcas por CPF: CNPJ**<br>`io.github.mcp-dir/inpi_marcas_titular-mcp` | INPI Marcas por CPF: CNPJ, official-source lookup. Platform-hosted, pay per query with prepaid credi | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi_marcas_titular-mcp) |
| **INPI: Patentes**<br>`io.github.mcp-dir/inpi_patentes-mcp` | INPI: Patentes, official-source lookup. Platform-hosted, pay per query with prepaid credit. | 0.1.0 | [dépôt](https://github.com/mcp-dir/inpi_patentes-mcp) |
| **Brazil Trademark Watch — INPI/RPI Monitor &amp; Deadline Alerts**<br>`io.github.paulovitorti/inpi-trademark-watch` | Monitors Brazilian trademarks at INPI and alerts on new RPI publications and deadlines. | 1.0.0 |  |
| **Monitor de Marcas INPI — Alertas da RPI por Processo**<br>`io.github.paulovitorti/rpi-inpi-delta-monitor-marcas` | Monitora marcas no INPI e alerta despachos novos da RPI por processo ou titular. | 1.0.0 |  |
| **Entreprises Fr**<br>`io.github.pipeworx-io/entreprises-fr` | French Companies MCP — recherche-entreprises.api.gouv.fr | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-entreprises-fr) · [site](https://pipeworx.io/packs/entreprises-fr) |
| **Insee**<br>`io.github.pipeworx-io/insee` | INSEE MCP — France's SIRENE business registry (INSEE). | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-insee) · [site](https://pipeworx.io/packs/insee) |
| `io.github.registry-desk/fr-sirene` | France Sirene company data. Batch Apify Actor, no hosted MCP endpoint. | 0.2.0 | [site](https://apify.com/tormanliikenne/fr-sirene) |
| **Gumball**<br>`io.github.thinkmojo/gumball` | Ring a real siren light from your AI agent: spin it, play a sound, set the volume, mute it. | 1.0.0 | [dépôt](https://github.com/thinkmojo/gumball-mcp) · [site](https://getgumball.com/ai-agents/) |
| **TrademarkIQ**<br>`io.github.trademarkiq/trademarkiq` | Marcas, patentes e desenhos do INPI e do mundo / INPI and worldwide trademarks, patents, designs | 1.0.3 | [dépôt](https://github.com/trademarkiq/trademarkiq-plugin) · [site](https://trademarkiq.com.br) |
| `io.github.yoavhizkiH/siren` | MCP server for Siren diagramming — teach AI models to create beautiful diagrams with Siren syntax | 0.1.0 | [dépôt](https://github.com/yoavhizkiH/siren) |
| **Vigilix: French company &amp; address data**<br>`io.github.zekinht0/vigilix-mcp` | French company, BODACC, address and hazard data for AI agents. Pay per call in USDC via x402. | 0.1.0 | [dépôt](https://github.com/zekinht0/vigilix-mcp) · [site](https://siren.vigilix.fr) |
| `io.insourcia/insourcia` | Search French companies: financials, directors, ownership, M&amp;A and insolvency events. | 1.0.0 | [site](https://insourcia.io/docs/guides/mcp) |
| **Veriport: EU VAT number, IBAN, VAT rates and e-invoice checks**<br>`io.veriport/veriport` | EU VAT numbers on VIES, IBANs, French companies, Peppol IDs, EU VAT rates and e-invoice validation. | 0.1.1 | [dépôt](https://github.com/zenomoung/veriport-sdk) · [site](https://veriport.io/docs) |
| **Open data: clinical trials, EU tenders, companies, fuel prices**<br>`org.duckdns.gregswarm.api/open-data` | Official open data as JSON: clinical trials, EU tenders, papers, apps, French firms, fuel, domains. | 1.0.20261027 | [site](https://swarminc12.github.io/) |

### Emploi et social (21)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Actual group – offres d'emploi**<br>`com.actualgroup/jobs` | Search Actual group job offers in France, find agencies and prepare applications. | 1.0.0 | [site](https://www.actualgroup.com/offres-emploi) |
| `com.adminlanding/french-rental-compliance` | FR/EN tools for French rental, frontalier &amp; home-employment (CCN 3239) — sourced, dated answers. | 1.20.0 | [site](https://adminlanding.com) |
| **Hosco Hospitality Jobs**<br>`com.hosco/hospitality-jobs` | Search Hosco's live hospitality jobs across Spain, Italy, France, the UK and Switzerland. | 1.0.0 | [site](https://www.hosco.com) |
| **Vocaneo**<br>`com.vocaneo/vocaneo` | French job profiles, RNCP diplomas and 135,000 training programs. Read-only, no account. | 1.0.1 | [dépôt](https://github.com/CIB-PROD/vocaneo-mcp-connector) · [site](https://vocaneo.com/connecteur) |
| **Brandyze**<br>`fr.brandyze/mcp` | French AI marketing intelligence: creators, brands, social listening — 158 MCP tools. | 1.0.0 | [site](https://brandyze.fr) |
| **Diplomètre**<br>`fr.diplometre/diplometre` | Free figures on French higher-education programs: admission, success, jobs, pay and cost, sourced. | 1.1.0 | [site](https://diplometre.fr/api-et-donnees) |
| **Marcel**<br>`fr.marcel-retraite/marcel` | Calcul fiscal et retraite français, déterministe et sourcé. Aucun conseil. | 1.4.14 | [dépôt](https://github.com/marcel-retraite/marcel-mcp) · [site](https://marcel-retraite.fr/developer) |
| **QuiRecrute — offres d'emploi en France**<br>`fr.quirecrute/offres-emploi` | Offres d'emploi en France (sites carrière et autres sources), sans compte. Job offers in France. | 1.0.2 | [site](https://quirecrute.fr) |
| **SignaMail**<br>`fr.signamail/signamail` | Email signatures for French businesses: HTML generator, legal mentions per job, SPF/DKIM/DMARC | 1.0.0 | [site](https://www.signamail.fr/serveur-mcp) |
| **Welcome to the Jungle Jobs Scraper &amp; API - Otta, WTTJ, Salary**<br>`io.github.AnshumanAtrey/welcome-to-the-jungle-jobs-search` | Welcome to the Jungle Jobs Scraper - France &amp; Europe | 1.0.10 | [dépôt](https://github.com/AnshumanAtrey/welcome-to-the-jungle-jobs-search) · [site](https://apify.com/anshumanatrey/welcome-to-the-jungle-jobs-search) |
| `io.github.JoJoLaBagarre/france-travail-mcp` | Serveur MCP des API officielles France Travail : offres d'emploi, ROME 4.0 et ROMEO. | 0.1.1 | [dépôt](https://github.com/jojolabagarre/france-travail-mcp) |
| `io.github.herve-coulon/tax-retirement` | Calcul fiscal (IR, IFI, PER, plus-value) et retraite français — 32 régimes, sourcé et daté. | 0.1.2 | [dépôt](https://github.com/herve-coulon/qotien-mcp) · [site](https://qotien.fr) |
| **Jobs and market data: France, Germany, Spain, Latin America**<br>`io.github.morvanvivien/bibian-data` | Public job postings and market data for France, Germany, Spain and Latin America, as JSON. | 1.0.1 | [dépôt](https://github.com/morvanvivien/bibian-data-mcp) |
| **French job boards: Free-Work, HelloWork, France Travail, WTTJ**<br>`io.github.morvanvivien/bibian-france-jobs` | Job postings and freelance day rates from 8 French job boards, as JSON tools. | 1.0.1 | [dépôt](https://github.com/morvanvivien/bibian-data-mcp) · [site](https://github.com/morvanvivien/bibian-data-mcp/tree/main/servers/france-jobs) |
| **OfferMagnet Jobs**<br>`io.offermagnet/jobs` | Live job search (US, France, Europe, remote) with each posting's visa-sponsorship stance. Free. | 1.0.0 | [site](https://offermagnet.io/developers) |
| **Yamagishi Sync Lab: wages and business risks**<br>`jp.aluzza/wages-risk` | Same-job hourly wages in Japan, US, UK, Germany, France; ranks of 31 business risks, Japan vs US. | 0.1.1 | [site](https://www.aluzza.jp/mcp-info/) |
| **GetYourJob**<br>`pro.getyourjob/getyourjob` | French IT job search, AI candidate-offer matching, application tracking and cover letters. | 1.0.0 | [site](https://getyourjob.pro/mcp) |
| **Rebond, offres d'emploi**<br>`pro.rebond/offres-emploi` | Offres d'emploi publiques en France : recherche par métier, ville, contrat, télétravail. | 1.0.0 | [site](https://rebond.pro) |
| **while**<br>`sh.while/while` | French PaaS built for AI agents: deploy apps, PostgreSQL, caches, S3 and jobs over MCP. | 2.1.0 | [site](https://while.sh/en/) |
| **Finder Jobs**<br>`work.finder/jobs` | Live job vacancies and salary stats: Russia, Belarus, Kazakhstan, Germany, France and more. | 1.2.0 | [site](https://mcp.finder.work/) |
| **Pionra**<br>`world.pionra/pionra` | Guides, housing, jobs, events and communities for foreigners living in France, in 6 languages. | 1.0.0 | [site](https://pionra.world) |

### Santé (5)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Anteos Health — Medical Appointment Booking**<br>`io.github.Anteos-Health/anteos-booking` | Book medical appointments with French doctors by specialty and city via AI agents. | 1.2.1 | [dépôt](https://github.com/Anteos-Health/anteos-mcp) · [site](https://anteoshealth.com/docs/mcp) |
| **Service Public France**<br>`io.github.OneNicolas/mcp-service-public` | French public services: tax, property, admin, education, healthcare, security, risks, legal texts | 1.14.8 | [dépôt](https://github.com/OneNicolas/mcp-service-public) · [site](https://mcp-service-public.nhaultcoeur.workers.dev) |
| **Médicaments en France**<br>`io.github.amine7536/medicaments-france` | French medicines (ANSM + BDPM): boxes and prices, prescription conditions, safety info, shortages. | 1.14.0 | [dépôt](https://github.com/amine7536/mcp-medicaments-france) · [site](https://medicaments-france.a3e.dev/) |
| `io.github.cturkieh/france-data-mcp` | French public-data MCP: cross-ref health, demographics, business, geo &amp; real-estate. | 0.32.0 | [dépôt](https://github.com/cturkieh/france-data-mcp) |
| **Restaurant Health Inspections**<br>`io.github.iangcarroll/restaurant-health` | Restaurant health inspection grades and violations for 20 US, UK, Canadian and French locations. | 1.0.0 | [dépôt](https://github.com/iangcarroll/food) · [site](https://food.ian.sh) |

### Juridique et administration (26)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **ConformRadar**<br>`com.conformradar/conformradar` | Check online shops and pages against French e-commerce and consumer law, with legal references. | 0.1.0 | [site](https://conformradar.com/en/ai-agents) |
| **Olia**<br>`com.kema-studio/olia` | Practice management for French law firms: matters, time, invoices and expenses from your AI agent. | 1.1.0 | [site](https://olia-app.fr) |
| **Legitym — French law**<br>`com.legitym/legal` | French case law (Judilibre) and codes (Légifrance) from official sources, with citation checking. | 1.0.1 | [site](https://legitym.com/fr/mcp?utm_source=mcp-registry&utm_medium=directory) |
| **Vitef**<br>`com.vitef/vitef` | French admin chores: law-citing letters, calculators, quotes. Démarches en France, vite fait. | 1.2.0 | [site](https://vitef.com/assistants-ia) |
| **Ansvar: French Law**<br>`eu.ansvar/french-law` | French law via Ansvar Gateway. Cited, OAuth + paid tier. | 1.0.737544ae | [dépôt](https://github.com/Ansvar-Systems/ansvar-gateway) · [site](https://ansvar.eu) |
| **Citexact : jurisprudence administrative française**<br>`fr.citexact/citexact` | Jurisprudence administrative française vérifiée (CE, CAA, TA), citée mot pour mot, liens Légifrance. | 1.0.0 | [site](https://citexact.fr/mcp) |
| **MCP Devis**<br>`fr.custom-digital-services/mcp-devis` | Calculate French quote totals and VAT, list legal mentions, and render printable HTML quotes. | 1.0.0 | [site](https://www.custom-digital-services.com/outils/devis/mcp/) |
| **LexIA Juris (EDIVIA)**<br>`fr.edivia/lexia-juris` | MCP droit &amp; fiscalite francais : 76 codes, jurisprudence, BOFiP. LexIA Juris, EDIVIA. Abonnement. | 1.0.0 | [site](https://edivia.fr/connecteur-mcp) |
| **Infoparcelle - cadastre, DVF, DPE, PLU**<br>`fr.infoparcelle/infoparcelle` | French property data: cadastral parcels, DVF sales, DPE, PLU zoning, permits, legal owners | 1.0.0 | [site](https://www.infoparcelle.fr/mcp) |
| **BRANCO - Droit français**<br>`fr.juan-branco/branco` | Droit français : textes, jurisprudence, citations et calculs. Connexion OAuth au compte BRANCO. | 0.56.3 | [site](https://droit.juan-branco.fr/) |
| **LibreJustice**<br>`fr.librejustice/librejustice` | Search French-language and European case law and French legal texts (codes, statutes, treaties). | 1.18.0 | [dépôt](https://github.com/librejustice/librejustice) · [site](https://librejustice.fr) |
| **Ma Copro En Règle**<br>`fr.macoproenregle/macoproenregle` | French condominiums (copropriétés): legal compliance check, managing agents, communes. | 1.0.0 | [site](https://www.macoproenregle.fr) |
| **Miramo**<br>`fr.miramo/mcp` | French rental law for tenants and landlords: rent caps, rental zones and unfurnished lease drafts. | 0.1.1 | [dépôt](https://github.com/miramolabs/mcp) · [site](https://www.miramo.fr/outils/mcp/developpeurs) |
| `fr.synergieloc/immobilier` | French real-estate law and building CAD/BIM (DXF, IFC). /mcp/v2: lightweight catalogue. | 1.2.0 | [site](https://synergieloc.fr/api-ia) |
| **Code civil (France)**<br>`io.arxo/code-civil` | French Civil Code (LEGI) as executable rules: facts and a legal date in, answers with grounds out | 0.1.10 | [dépôt](https://github.com/arxohq/arxo-code-civil) · [site](https://code-civil.law.arxo.io/) |
| **Droit français sourcé**<br>`io.github.JohnJackHouzi/droit-francais` | Droit français et européen officiel (Légifrance, DILA, EUR-Lex, jurisprudence), réponses sourcées. | 1.0.0 | [dépôt](https://github.com/JohnJackHouzi/droit-francais-skill) · [site](https://droit.studjoow.com) |
| **AiLexLux**<br>`io.github.digitranslu/ailexlux` | Luxembourg, EU and French legal research and drafting with cited sources. Account required. | 1.0.0+87d3a9e8e283 | [site](https://ailexlux.digitrans.lu) |
| `io.github.kOlapsis/gofact` | Creates compliant French e-invoices locally: Factur-X PDF/A-3 with EN 16931 CII XML, legal numbering | 1.2.0 | [dépôt](https://github.com/kOlapsis/gofact) · [site](https://gofact.kolapsis.com/) |
| `io.github.matematicsolutions/fr-eli-mcp` | MCP server for French Legifrance (PISTE): legislation and case law with citations and ECLI. | 0.5.3 | [dépôt](https://github.com/matematicsolutions/fr-eli-mcp) |
| `io.github.matematicsolutions/mcp-fr-legal` | Offline MCP korpus prawa francuskiego (Legifrance/DILA) - verbatim + grounding cytatu | 0.1.3 |  |
| **Légifrance (unofficial)**<br>`io.github.muchiny/mcp-legifrance` | Unofficial MCP server for French law via the Légifrance API (PISTE): search, read and cite texts. | 1.1.1 | [dépôt](https://github.com/muchiny/mcp-legifrance) · [site](https://github.com/muchiny/mcp-legifrance#readme) |
| **French Legal Data (Law, Case Law, IDCC)**<br>`io.github.nicolasfichaux1-blip/french-legal-mcp` | French law articles, codes, collective agreements, case law (Judilibre) and IDCC lookup. | 1.0.0 | [site](https://apify.com/klyve1/french-legal-mcp) |
| **France Legi**<br>`io.github.pipeworx-io/france-legi` | France LEGI — the official consolidated French codes (Code civil, Code pénal, Code du travail… | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-france-legi) · [site](https://pipeworx.io/packs/france-legi) |
| **madda — Moroccan law, article by article**<br>`ma.madda/law` | Search and read Moroccan law article by article, in Arabic and French, with citations and PDFs. | 1.0.0 | [site](https://madda.ma) |
| **Clair**<br>`org.dpdns.clair/clair` | Audits AI-built websites for France and Belgium: cookies, GDPR, legal pages, exposed keys, security. | 1.0.0 | [site](https://clair.dpdns.org) |
| **Qanun: Tunisian law**<br>`tn.qanun/qanun-mcp` | Search Tunisian law and JORT announcements in Arabic and French, with article and JORT citations. | 1.0.0 | [site](https://qanun.tn) |

### Fiscalité et facturation (39)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Altoviz MCP Server**<br>`com.altoviz/mcp` | Altoviz invoicing app for french small businesses | 1.0.8 | [site](https://mcp.altoviz.com) |
| **E-Invoice Checker**<br>`com.einvoicechecker/einvoice` | Validate, parse and render EU e-invoices: XRechnung, ZUGFeRD, Factur-X, Peppol BIS 3.0, KSeF. | 1.0.2 | [dépôt](https://github.com/vpukhalskyi/einvoicechecker-mcp) · [site](https://einvoicechecker.com/mcp/) |
| `com.einvoicewiz/einvoicewiz` | Generate &amp; validate EN 16931 e-invoices (Factur-X, ZUGFeRD, XRechnung); verification certificates | 1.0.0 | [site](https://einvoicewiz.com/mcp-server) |
| **FacturX API**<br>`com.facturxapi/mcp` | Official MCP server for the FacturX API: dry run, Factur-X conversion, validation, rule lookup. | 0.1.2 | [dépôt](https://github.com/facturxapi/sdk) |
| **InvoiceXML**<br>`com.invoicexml/invoicexml` | Create, validate, convert &amp; extract compliant e-invoices (UBL, Factur-X, ZUGFeRD, XRechnung) | 1.0.0 | [dépôt](https://github.com/InvoiceXML/invoicexml-mcp) · [site](https://www.invoicexml.com/mcp-server) |
| **Margeo**<br>`com.margeoapp/mcp` | Margeo gives an AI agent a French reseller's stock, sales, net margin and micro-enterprise tax. | 1.0.0 | [site](https://margeoapp.com) |
| `com.tally-stick/tallystick` | Read, validate and write e-invoices on your own machine. XRechnung, ZUGFeRD, Factur-X, UBL, CII. | 1.0.2 | [dépôt](https://github.com/nugrohosetiaji91-png/tallystick-mcp) · [site](https://tally-stick.com/mcp) |
| **VoxFactura**<br>`com.voxfactura/voxfactura` | Quotes, invoices, French e-invoicing, website and bookings for French freelancers. | 1.0.0 | [site](https://www.voxfactura.com/assistants-ia) |
| **XSDHub: EN 16931, XRechnung, Factur-X, KSeF, RO and HR e-invoices**<br>`com.xsdhub/validation` | Official rules, version per result. SAF-T, SEPA, ISO 20022, DAC7 checks; CBAM and XBRL web tools. | 2.1.3 | [site](https://xsdhub.com/ai) |
| `cool.asap/mcp` | Drive ASAP: French invoicing, contacts and e-signature from any MCP client (NF203-compliant). | 1.0.0 | [site](https://github.com/asap-cool/asap-agent-skills) |
| **Fakturai — German E-Invoice (ZUGFeRD / Factur-X / E-Rechnung)**<br>`de.fakturai/erechnung` | KoSIT-verified German e-invoices: generate &amp; validate ZUGFeRD, Factur-X, XRechnung, EN 16931. | 1.0.1 | [site](https://fakturai.de) |
| **finisma – ZUGFeRD e-invoices**<br>`de.finisma/finisma` | Create ZUGFeRD e-invoices from JSON or PDF, verify Factur-X, check VAT IDs and Peppol. | 1.0.1 | [site](https://finisma.de/entwickler/connector) |
| **csv2zugferd**<br>`de.zugferd-tools/csv2zugferd` | csv2zugferd setup assistant: drafts and checks the CSV to ZUGFeRD/Factur-X mapping, local via stdio | 1.3.1 | [dépôt](https://github.com/rillsoft/csv2zugferd) · [site](https://zugferd-tools.de/docs/mcp-server/) |
| **ZUGFeRD Validator**<br>`de.zugferd-validator/validator` | Validiert E-Rechnungen (ZUGFeRD/Factur-X, XRechnung) gegen EN 16931 mit Korrekturvorschlägen. | 1.0.2 | [dépôt](https://github.com/tzatti1/zugferd-validator) · [site](https://zugferd-validator.de) |
| `dev.invoicehub/invoicehub` | Validate, generate &amp; convert EU e-invoices (UBL, CII, XRechnung, Factur-X) — EN 16931 pre-validated. | 1.3.0 | [dépôt](https://github.com/einvoice-dev1/einvoice) · [site](https://invoicehub.dev) |
| **invowerk**<br>`dev.invowerk/xrechnung-e-invoice-validator` | E-invoice checker for ZUGFeRD, Factur-X, XRechnung and Peppol BIS. Free web tool, API and MCP. | 1.0.1 | [dépôt](https://github.com/invowerk-dev/invowerk) · [site](https://invowerk.dev/en/) |
| **Fiscalité française ouverte, par BENSAID Avocats**<br>`fr.bensaid-avocats/fiscalite` | Official French tax texts, dated and sourced: 237 treaty instruments by article, BOFiP, CGI, LPF. | 1.0.0 | [site](https://outils.bensaid-avocats.fr/mcp) |
| **gentry.fr, le logement en France**<br>`fr.gentry/logement` | French housing data for every commune: sale prices, rents, property tax and rent control. | 0.2.0 | [site](https://gentry.fr/agents) |
| **Omexis Invoice**<br>`fr.omexis/invoice` | Facturation française : devis, factures Factur-X, avoirs, relances et comptabilité. | 1.2.61 | [site](https://invoice.omexis.fr/assistant-ia) |
| **PocketPlanner**<br>`fr.pocketplanner/pocketplanner` | Let your AI assistant read and fill your finances: budget, statements, payslips, taxes (French). | 1.0.0 | [site](https://pocketplanner.fr) |
| **Vérifier ma facture (Factur-X)**<br>`fr.verifier-ma-facture/validateur` | Conformité Factur-X : 21 critères PDF/A-3, XML CII EN 16931 et BR-FR. Analyse locale, gratuite. | 1.0.0 | [site](https://verifier-ma-facture.fr) |
| `io.github.CedricKouma/facturx-toolkit` | Validate, extract, repair and generate French Factur-X / EN16931 invoices via AgentForge API | 0.1.0 |  |
| `io.github.CloudCorpRecords/invoiceproof` | Validate, parse, fix and render EU e-invoices: XRechnung, ZUGFeRD, Factur-X, Peppol, KSeF. | 0.1.0 | [dépôt](https://github.com/CloudCorpRecords/invoiceproof) |
| **LayerOne (DocX + Sign)**<br>`io.github.GOLayerone/layerone` | MCP server for LayerOne DocX (documents &amp; Factur-X) and Sign (e-signature) APIs. | 1.0.0 | [dépôt](https://github.com/GOLayerone/layerone-mcp) · [site](https://dev.layerone.fr) |
| **EU E-Invoice XML Generator (Peppol, XRechnung, Factur-X)**<br>`io.github.Geornaden/peppol-xrechnung-facturx-einvoice-mcp` | Generate EN 16931 European B2B e-invoice XML in Peppol BIS 3.0 UBL, XRechnung 3.0 &amp; Factur-X CII | 1.1.0 | [dépôt](https://github.com/Geornaden/finops-studio) · [site](https://geornaden.github.io/finops-studio/eu-einvoice-xml-generator.html) |
| **Factur-X: Generate &amp; Validate E-Invoices**<br>`io.github.LeBorgneAntoine/facturx` | Validate Factur-X, CII &amp; UBL e-invoices free, no key. Generate compliant invoices from JSON. | 0.1.23 | [site](https://facturx.orvel.dev/docs/mcp/) |
| `io.github.OlivierLAVAUD/mcp-immo` | French real-estate intelligence from official open data: DVF, valuations, rents, tax, DPE, risks. | 1.12.0 | [dépôt](https://github.com/OlivierLAVAUD/mcp-immo-olv) |
| **Conformo e-invoicing**<br>`io.github.arsalmurad/conformo` | Create and check EU e-invoices (XRechnung, ZUGFeRD, Factur-X, Peppol) locally. Free, no API key. | 0.2.5 | [dépôt](https://github.com/arsalmurad/conformo) · [site](https://conformo-docs.vercel.app/) |
| `io.github.causa-prima-ai/scribo` | EN 16931-compliant e-invoices: XRechnung, ZUGFeRD, Factur-X, Peppol BIS, Facturae or US PDF. Free. | 1.0.0 | [dépôt](https://github.com/causa-prima-ai/scribo-mcp) · [site](https://scribo.causaprima.ai) |
| **Facture Électronique France**<br>`io.github.cmendezs/mcp-facture-electronique-fr` | MCP server for French e-invoicing (XP Z12-013). Manages invoices, validation and compliance. | 0.9.1 | [dépôt](https://github.com/cmendezs/mcp-facture-electronique-fr) |
| **Eleata e-Invoice Validator**<br>`io.github.hernaninverso/eleata-einvoice-mcp` | Validate EU e-invoices (Peppol, XRechnung, Factur-X, UBL, CII) and explain error codes. | 0.1.1 | [dépôt](https://github.com/hernaninverso/eleata-einvoice-mcp) |
| `io.github.imysfylmz/xrechnung-mcp` | Validate and parse German e-invoices (XRechnung, ZUGFeRD, Factur-X) offline. No API key. | 0.1.1 | [dépôt](https://github.com/imysfylmz/e-rechnung-inbox) · [site](https://www.e-rechnung-inbox.de) |
| `io.github.jmshinhwa/france-einvoice-reception-lint` | 12 checks for the French mandatory mentions a PDP rejects, on Factur-X, UBL and CII invoices | 1.0.5 | [dépôt](https://github.com/jmshinhwa/readystack-themes) |
| **OpenLMNP**<br>`io.github.manganate006/openlmnp` | Accounting MCP for French LMNP furnished rentals: income, expenses, depreciation, tax return &amp; FEC | 1.6.7 | [dépôt](https://github.com/manganate006/openlmnp) · [site](https://openlmnp.fr/mcp) |
| **InvoiceIn**<br>`io.github.peculiar-systems/invoicein` | XRechnung, Factur-X, Peppol, PINT AE, FatturaPA, KSeF → JSON + fix hints. Hosted; MIT=examples only | 0.1.3 | [dépôt](https://github.com/peculiar-systems/invoicein-examples) · [site](https://invoicein.peculiar.systems) |
| **pdf-to-einvoice**<br>`io.github.smart-byte/pdf-to-einvoice` | Invoice PDF to EN 16931 e-invoice (ZUGFeRD / Factur-X), or invoice data to XRechnung. Runs locally. | 0.2.0 | [dépôt](https://github.com/smart-byte/pdf-to-einvoice) · [site](https://pdf-to-einvoice.app/) |
| **FEC Checker (French accounting file pre-audit)**<br>`io.github.vincenz-cdf/fec-lint` | Validate French FEC accounting files and flag audit risks before a tax audit. | 0.1.2 | [dépôt](https://github.com/vincenz-cdf/fec-lint) · [site](https://apify.com/highfiddle/fec-checker) |
| **XRechnung Validator**<br>`io.github.wenboteng/xrechnung-validator` | Validate XRechnung XML and ZUGFeRD / Factur-X PDFs with the free checkxrechnung.com API. | 0.2.0 | [dépôt](https://github.com/wenboteng/xrechnung-validator) · [site](https://checkxrechnung.com) |
| **France Salary and Income Tax Calculator**<br>`nl.nltaxcalculator/salary-fr` | France 2026: full income tax return, net salary, gross for net, employer cost, vs the Netherlands | 2.1.0 | [site](https://nltaxcalculator.nl/dutch-tax/ai-assistants) |

### Transport et mobilité (9)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Andiamo**<br>`app.getandiamo/andiamo` | Plan door-to-door trips across French trains, buses, transit and flights. | 0.3.0 | [dépôt](https://github.com/NicoLeblanc/andiamo) · [site](https://getandiamo.app) |
| **France Logistics (buy Mondial Relay + more labels + tracking via Shippo)**<br>`app.wishpool/france-logistics-mcp` | France shipping labels for AI agents: buy Mondial Relay and more, track and refund via Shippo. | 0.1.0 | [dépôt](https://github.com/junter1989k-ai/france-logistics-mcp) |
| **Fulfiller — print catalogue &amp; quotes**<br>`com.fulfiller/agentic-commerce-api` | Print catalogue and live quotes from Fulfiller, a French online printer shipping across the EU. | 0.1.3 | [site](https://www.fulfiller.com/mcp.html) |
| **Aucadastre**<br>`fr.aucadastre/aucadastre` | A French address, all public facts: parcel, zoning, risks, permits, sales, energy labels. No key. | 0.1.0 | [site](https://aucadastre.fr/api) |
| **SYMONE motorcycle transport**<br>`fr.symone/symone-moto` | Motorcycle transport in France: search trips, request quotes, prepare bookings the customer pays. | 1.4.0 | [site](https://symone.fr/developers) |
| **PLU IA**<br>`io.github.AZERIA-IT/pluia` | French zoning (PLU) for any address or parcel in France, rules cited with the official PDF page. | 1.0.0 | [site](https://pluia.fr/ia) |
| **GBFS France**<br>`io.github.Timi-Duban/gbfs-france` | Live bike-share availability in France: docked networks, nearby stations, bikes and docks. | 0.1.5 | [dépôt](https://github.com/Timi-Duban/gbfs-france) |
| **Data Sncf**<br>`io.github.pipeworx-io/data-sncf` | SNCF Open Data (ressources.data.sncf.com) — OpenDataSoft MCP. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-data-sncf) · [site](https://pipeworx.io/packs/data-sncf) |
| **Parcellaire — cadastre, DVF &amp; DPE**<br>`re.parcellai/parcellaire` | French real estate data: cadastre, DVF sales, DPE energy ratings, price estimates, parcel context | 1.2.0 | [site](https://parcellai.re/mcp) |

### Géographie et territoire (12)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **France Payments (Stripe — Cartes Bancaires)**<br>`app.wishpool/france-payments-mcp` | France payments for AI agents — Cartes Bancaires via Stripe. Never holds funds. | 0.1.0 | [dépôt](https://github.com/junter1989k-ai/france-payments-mcp) |
| **Callejear France**<br>`com.callejear/france` | Search 2.19M French street records, IRIS-level amenities and commune-level risk codes. | 1.0.0 | [site](https://callejear.fr/mcp/) |
| **TOISE — intelligence foncière**<br>`com.toiseai/toise` | French land intelligence: corporate property owners, zoning (PLU), cadastre, DVF sales, permits. | 1.0.0 | [site](https://toiseai.com) |
| **Chaque Euro**<br>`fr.chaqueeuro/mcp` | Argent public français avec sources : budget de l'État, communes, marchés publics, élus. | 1.0.0 | [dépôt](https://github.com/ArthurVerrez/chaque-euro) · [site](https://chaqueeuro.fr/ia) |
| **LexIA Pro (EDIVIA)**<br>`fr.edivia/lexia-pro` | MCP foncier &amp; urbanisme officiels francais (cadastre, PLU, DVF). LexIA Pro, EDIVIA. Abonnement. | 1.0.0 | [site](https://edivia.fr/connecteur-mcp) |
| **Info Scolarité**<br>`fr.info-scolarite/etablissements` | Écoles, collèges et lycées de France : fiche, recherche et collège de secteur d'une adresse. | 1.0.0 | [site](https://info-scolarite.fr/mcp) |
| **La Grande Carte**<br>`fr.lagrandecarte/mcp` | French open data: communes, parcels, risks, property sales, water, crime, planning. | 1.0.0 | [site](https://lagrandecarte.fr/api-doc#mcp) |
| **Mitoyens, pesticides près de chez vous**<br>`fr.mitoyens/pesticides` | Pesticides par commune en France : eau du robinet, écoles près des champs, achats, carte. | 1.1.0 | [site](https://mitoyens.fr/donnees-api) |
| **Sentinelle catnat FR**<br>`io.github.Marcoco27/sentinelle-catnat` | French natural-disaster decrees (CatNat) per commune, with the open insurance-claim deadline. | 0.2.0 | [site](https://catnat.amacunia.fr/) |
| **Ban Fr**<br>`io.github.pipeworx-io/ban-fr` | Base Adresse Nationale (BAN) MCP — France's official keyless geocoding API… | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-ban-fr) · [site](https://pipeworx.io/packs/ban-fr) |
| **INSEE commune code shape, value discarded**<br>`io.github.sadri-dridi/fr-insee` | INSEE commune code shape, value discarded | 1.0.0 | [dépôt](https://github.com/sadri-dridi/named-mcp-utilities) |
| **André — Analyse électorale française**<br>`vote.andre/elections` | Résultats des élections françaises 1999-2026 par bureau de vote, socio-démo INSEE et cartes. | 1.28.1 | [dépôt](https://github.com/joelgombin/andre-mcp) · [site](https://andre.vote) |

### Données publiques (12)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Immo API — DVF immobilier France**<br>`app.immoapi/immoapi` | Données immobilières DVF France : transactions, comparables GPS et statistiques de marché. | 1.0.0 | [site](https://immoapi.app) |
| **BOFiP — Doctrine fiscale française**<br>`dev.bofip/bofip` | Doctrine fiscale française (BOFiP) : recherche, veille et graphe doctrinal. Etalab 2.0. | 1.0.0 | [dépôt](https://github.com/alexandreCyber/bofip) · [site](https://bofip.dev/mcp) |
| **LyBox**<br>`fr.lybox/lybox` | Investissement locatif en France : annonces, rendement, cash-flow, données INSEE et marché par ville | 1.0.0 | [site](https://www.lybox.fr) |
| `io.github.benliberte/immo` | InvestScore immobilier France (DPE, DVF, risques, INSEE), payé à la requête en USDC via x402. | 0.1.2 | [dépôt](https://github.com/bdatax-x/dpe-x402) |
| `io.github.lacausecrypto/mcp-new-caledonia` | MCP server for New Caledonia open data (data.gouv.nc) | 1.0.2 | [dépôt](https://github.com/lacausecrypto/mcp-new-caledonia) |
| `io.github.mrfentmen/data-gouv-fr-mcp` | data.gouv.fr: search French open datasets with producers and update dates - keyless. | 1.0.0 | [dépôt](https://github.com/mrfentmen/awesome-servers) |
| `io.github.mrfentmen/data-gouv-mcp` | French open data from data.gouv.fr. No key required. | 1.0.1 | [dépôt](https://github.com/mrfentmen/data-gouv-mcp) |
| **Data Economie Fr**<br>`io.github.pipeworx-io/data-economie-fr` | France Economy &amp; Finance Open Data (data.economie.gouv.fr) — OpenDataSoft MCP. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-data-economie-fr) · [site](https://pipeworx.io/packs/data-economie-fr) |
| **Data Iledefrance**<br>`io.github.pipeworx-io/data-iledefrance` | Île-de-France Open Data (data.iledefrance.fr) — OpenDataSoft MCP. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-data-iledefrance) · [site](https://pipeworx.io/packs/data-iledefrance) |
| **Data Newcaledonia**<br>`io.github.pipeworx-io/data-newcaledonia` | New Caledonia Open Data (data.gouv.nc) — OpenDataSoft MCP. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-data-newcaledonia) · [site](https://pipeworx.io/packs/data-newcaledonia) |
| **Datagouv Fr**<br>`io.github.pipeworx-io/datagouv-fr` | data.gouv.fr MCP — French open-data catalogue. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-datagouv-fr) · [site](https://pipeworx.io/packs/datagouv-fr) |
| **Créavores AEO Audit**<br>`io.github.tresor4k/creavores-aeo-mcp` | AEO audit of any URL + French open data: website prices, public tenders, editorial calendar. | 1.1.0 | [dépôt](https://github.com/tresor4k/creavores-aeo-mcp) · [site](https://lescreavores.fr) |

### Commerce et paiement (35)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **MORYA**<br>`app.morya/morya` | French real-estate intelligence: prices, rents, listings, yield, rental management. | 1.0.0 | [site](https://morya.app) |
| **Angling Lines**<br>`com.anglinglines/angling-lines` | Carp fishing lakes in France: search venues, check availability, get week prices, book. | 0.1.0 | [site](https://www.anglinglines.com) |
| **Catalogue APP24, formations d'échecs en français**<br>`com.apprendre-les-echecs-24h/catalogue-echecs-fr` | 94 formations d'échecs en français, prix à jour, conseil d'entraîneur. French chess courses. | 1.0.0 | [site](https://boutique.apprendre-les-echecs-24h.com/) |
| **B.R.M Chronographes - Watch Configurator**<br>`com.brm-chronographes/configurator` | Configure, price, render and order a made-to-order B.R.M watch (French manufacture). No auth. | 1.0.0 | [site](https://brm-chronographes.com/en/ai-assistants.html) |
| **Chateaupedia**<br>`com.chateaupedia/chateaux` | Search 10,500 French castles and read verified entries: hours, prices, accessibility. | 1.0.0 | [site](https://chateaupedia.com) |
| `com.gositehunt/gositehunt` | Development-site hunting by chat: sites, deal checks, billboards, retail catchments, French friches. | 1.4.0 | [site](https://gositehunt.com/claude) |
| **Losali French Special Numbers**<br>`com.losalidirect/numbers` | Directory of French special phone numbers: service, number type, cost from France, Losali price | 1.1.0 | [site](https://api.losalidirect.com/v1/docs) |
| **ski-france.com Agent Search**<br>`com.ski-france/agent-search` | Ski apartment search with one- or two-week prices, availability, destinations and ski passes. | 1.1.0 |  |
| **Washlib**<br>`com.washlib/washlib` | Find car washes in France, check live slots, get exact prices and start a booking. | 1.1.0 | [dépôt](https://github.com/J1034567/washlib) · [site](https://washlib.com) |
| **Software Prices France**<br>`eu.jaikin/software-prices-france` | Read-only French software project prices: public contracts (DECP) and verified private figures. | 0.2.2 | [dépôt](https://github.com/Jaikin-SASU/claude-plugin-software-buyer) · [site](https://www.jaikin.eu/) |
| **BocaRoss**<br>`fr.bocaross/annuaire` | Garages, carrossiers, contrôle technique, pare-brise et lavage auto en France : fiches et prix. | 1.0.0 | [site](https://bocaross.fr) |
| **Cadorama**<br>`fr.cadorama02/shop` | Asian grocery &amp; gift shop in France: search products, promos, store hours, Stripe checkout links. | 1.1.0 | [site](https://www.cadorama02.fr) |
| **CarHunt**<br>`fr.carhunt/carhunt` | French used-car search across LeBonCoin, LaCentrale, L'Argus and more, with price estimates. | 1.0.0 | [site](https://carhunt.fr/mcp) |
| **Chaletiste**<br>`fr.chaletiste/chaletiste` | Compare French wooden kit cabins: prices, sizes, assembly time, delivery, budget and permits. | 1.0.0 | [site](https://chaletiste.fr/developpeurs/) |
| **Château du Bû**<br>`fr.chateaudubu/chateau-du-bu` | Read-only hotel server: room availability and prices, hotel facts, public events. Normandy, France. | 1.1.0 | [site](https://chateaudubu.fr/en/) |
| **Comparr**<br>`fr.comparr/comparr` | Compare prices across French merchants, with recorded price history and verified price drops. | 1.1.0 | [site](https://www.comparr.fr) |
| **Dinilu France — objets publicitaires personnalisés**<br>`fr.dinilu/mcp` | Prix actuels, options et frais de port pour des objets publicitaires personnalisés. | 1.0.0 | [site](https://dinilu.fr/qui-sommes-nous/developpeurs) |
| **Estimatiz**<br>`fr.estimatiz.www/estimatiz` | French property estimates from DVF sales, with price range, limitations and a shareable report. | 1.0.0 | [site](https://www.estimatiz.fr/api-documentation) |
| **Lingoris — traduction assermentée**<br>`fr.lingoris/traduction-assermentee` | Sworn (certified) translation in France: instant firm price, 100+ languages, order tracking. | 1.0.0 | [site](https://www.lingoris.fr/mcp) |
| **rendita**<br>`fr.rendita/rendita` | French dividends read in the filings (BALO, AMF): amount, ex-date, payment date, quote. No key. | 0.1.0 | [site](https://rendita.fr/api) |
| **SCPI Select**<br>`fr.scpiselect/scpi-select` | French SCPI real estate funds: prices, yields, fees and acquisitions from official reports. | 1.0.0 | [site](https://scpiselect.fr/brancher-votre-ia) |
| **Sous La Gélule**<br>`fr.souslagelule/complements` | Food supplements sold in France ranked by price per month at the same dose, with source and date. | 1.0.0 | [site](https://souslagelule.fr/donnees) |
| **nextbank.holiday**<br>`holiday.nextbank/bank-holidays` | Next bank holiday for UK, Ireland, France, Germany and Spain. Free daily quota, then USDC via x402. | 1.0.0 |  |
| **Pryx**<br>`io.github.KilianPA/pryx` | Buying advice for the French market: which appliance or electronics to buy, with live prices. | 0.5.0 | [dépôt](https://github.com/KilianPA/pryx-mcp) · [site](https://pryx.fr) |
| **FlatWay Property Estimator**<br>`io.github.Lfaykus/flatway-estimate` | French property prices, market trends &amp; live listings for any French address. Powered by DVF data. | 1.2.0 | [site](https://flatway.fr) |
| **Official Fuel Prices**<br>`io.github.SSTEmpresarial/official-fuel-prices` | Live official fuel station prices for France, Spain, Italy and Western Australia in one schema. | 1.0.0 | [dépôt](https://github.com/SSTEmpresarial/official-fuel-prices-api-examples) · [site](https://fuel-prices-api.pages.dev/mcp/) |
| **Joomil — Swiss Classifieds MCP**<br>`io.github.ValmoDev/joomil` | Search Swiss classifieds on Joomil.ch — top marketplace in French-speaking Switzerland since 2007. | 1.0.0 | [dépôt](https://github.com/Valmo-Sarl/joomil-mcp) |
| **Adopte un Conteneur - Container Delivery &amp; Dimensions**<br>`io.github.adopteunconteneur/delivery-calculator` | Delivery cost calculator and container dimensions for France. Real HT/TTC prices. | 1.2.0 | [dépôt](https://github.com/adopteunconteneur/mcp-container-delivery) · [site](https://adopte-un-conteneur.fr/ai) |
| **ORIAS French Insurance Intermediaries**<br>`io.github.corent1robert/orias-insurance-intermediaries` | French ORIAS registry: insurance, banking and investment intermediaries with ID, status, address | 1.0.0 |  |
| **France Dvf**<br>`io.github.pipeworx-io/france-dvf` | france-dvf — France's official property-transfer price records ('Demandes de valeurs foncieres'… | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-france-dvf) · [site](https://pipeworx.io/packs/france-dvf) |
| **BankLabs**<br>`io.github.rodriguem75/banklabs` | French banking comparisons, documented fees, coverage and tariff history. | 1.0.1 | [site](https://www.banklabs.fr) |
| **CultureLib Discover**<br>`io.github.tbalcinha/culturelib-discover` | French cultural events search with multi-reseller ticket price comparison (theatre, concerts...) | 1.1.0 | [dépôt](https://github.com/tbalcinha/culturelib) |
| **Secrets.RealEstate**<br>`realestate.secrets/property-intelligence` | French address intelligence: 18.6M sold prices, energy, risk, crime and schools — each sourced. | 1.0.0 |  |
| **nophi.sh**<br>`sh.nophi/nophish` | Public facts on nophi.sh, phishing-awareness training for French SMBs: catalogue, prices, security. | 1.0.2 | [site](https://nophi.sh/en/ai-agents?utm_source=mcp-registry&utm_medium=directory) |
| **eglise.shop — Christian bookstore (FR/EN)**<br>`shop.eglise/librairie` | Christian bookstore (French/English): search books, prices in 12 currencies, cart, checkout link. | 1.1.0 | [site](https://eglise.shop/) |

### Autre (142)

| Serveur | Description | Version | Liens |
|---|---|---|---|
| **Art Déco Belgique**<br>`be.art-deco-belgique/art-deco-belgique` | Verified Art Deco buildings of Belgium and Hauts-de-France: search, nearby, architects, routes. | 1.0.0 | [dépôt](https://github.com/pjtribot-blip/rtdeco-belgique-) · [site](https://art-deco-belgique.be) |
| **MANA — la vie locale française**<br>`bzh.mana/mana` | Lieux, commerces, artisans et associations en France — 3,7 M de fiches, rangées par code NAF. | 1.0.0 | [site](https://mana.bzh) |
| **Revoir**<br>`ca.agenceelan/revoir` | French-language meeting records: transcript, shared-screen content, decisions and actions. | 1.0.0 | [site](https://revoir.video/docs/mcp) |
| **GeNEVERsleep**<br>`ch.geneversleep/events` | Read-only nightlife events for Geneva, Lausanne, Nyon, Romandy and neighbouring France. | 0.1.1 | [site](https://geneversleep.ch/api) |
| **Nautilinks Backlinks**<br>`co.nautilinks/nautilinks-mcp` | Search, compare, order and track French backlink placements through the Nautilinks catalog. | 1.0.0 | [site](https://nautilinks.co/fr/api/) |
| **CERT-FR Security Advisories — buy per-query in-session (certfrwatch)**<br>`com.a2awire/data-cert-fr-advisory-national-cert-alert` | CERT-FR (ANSSI) security advisories &amp; alerts: French national CERT vulnerability feed, hourly | 0.1.1 | [dépôt](https://github.com/ee324/a2awire) · [site](https://a2awire.com/data/certfrwatch-cert-fr-anssi-national-security-advisories-b65f45) |
| **AGORA Omega**<br>`com.agoraverify/agora-omega` | Pay-per-call checks for AI agents: sanctions, MiCA, French KYB, AI Act. USDC via x402. | 1.1.0 | [site](https://agoraverify.com) |
| **API Cinéma — movie showtimes**<br>`com.api-cinema/showtimes` | Movie showtimes for 5,000+ cinemas in France and the US: search cinemas, movies and screenings. | 1.0.0 | [site](https://api-cinema.com/en?utm_source=mcp-registry) |
| **CasaPrivat**<br>`com.casaprivat/casaprivat` | Find homes sold by owners in Spain and France. Messages to owners need buyer consent; rate-limited. | 1.0.0 | [site](https://casaprivat.com) |
| **Chalet-Montagne.com**<br>`com.chalet-montagne/rentals` | Search French mountain holiday rentals and contact owners directly — commission-free. | 1.2.0 | [dépôt](https://github.com/alpium/chalet-montagne-mcp) · [site](https://www.chalet-montagne.com/ia) |
| **Dialyne**<br>`com.dialyne/dialyne` | Real phone calls in France for AI agents: dial +33 numbers, menus, hold, AI disclosure. | 1.0.0 | [dépôt](https://github.com/dialyne/dialyne-mcp) · [site](https://dialyne.com) |
| `com.faireplace/public-tools` | French rental tools: create &amp; e-sign a lease, rent control, IRL, deposit, receipts (France). | 0.3.0 | [dépôt](https://github.com/ykarmouta/faireplace-public-mcp) · [site](https://faireplace.com) |
| **FiltreCharbon**<br>`com.filtrecharbon/catalogue` | Search FAC charcoal filters, check specifications and prepare a checkout link for France or Belgium. | 1.0.0 | [site](https://filtrecharbon.com/commander-avec-un-assistant) |
| **Furrsati**<br>`com.furrsati/mcp` | Search Furrsati's Lebanese freelancers by skill, rate and rating, in English, Arabic or French. | 0.1.0 | [site](https://furrsati.com/mcp) |
| **Grand Livre**<br>`com.gdlivre/grand-livre` | French bookkeeping for freelancers and small businesses: entries, ledger, trial balance, matching. | 1.0.0 | [site](https://gdlivre.com) |
| **Gestello**<br>`com.gestello/gestello` | CRM gratuit pour indépendants : clients, dossiers, rendez-vous, tâches et notes. En français. | 1.0.0 | [site](https://gestello.com/integration-chatgpt-claude) |
| **HornLib — Interactive French Horn F/Bb Double Horn Fingering &amp; Harmonics Engine**<br>`com.hornlib/mcp` | Look up French Horn F/Bb double horn valve fingerings, trigger combinations &amp; partials. | 1.0.3 | [dépôt](https://github.com/HornLib/mcp-server) · [site](https://hornlib.com) |
| **Humanuscrit — HAPP**<br>`com.humanuscrit/happ` | Submit a literary text in French to Humanuscrit, a publisher open to humans and AI agents. | 1.0.0 | [dépôt](https://github.com/maxcarriere/humanuscrit) · [site](https://humanuscrit.com/agents/) |
| `com.isoligne/index` | Ce qu'une IA lit des sites web français : note, axes, secteur, questions sans réponse. | 0.2.2 |  |
| **Meilleur Code Parrainage**<br>`com.meilleurcodeparrainage/codes-parrainage` | Codes de parrainage vérifiés en France : bonus filleul et parrain, conditions, fiche source. | 1.1.1 | [dépôt](https://github.com/Cryptomoon7/meilleur-code-parrainage-mcp) · [site](https://meilleurcodeparrainage.com/assistants-ia/) |
| **The Moorish Times**<br>`com.moorishtimes/moorish-times` | Search, read and cite The Moorish Times: Moroccan history, culture and news, in French and English. | 2.4.0 | [site](https://moorishtimes.com) |
| **Motif &amp; Moi**<br>`com.motifetmoi/motif-et-moi` | Custom clothing printed in Rennes, France: catalog, quotes, delivery dates, prefilled order links. | 1.0.0 | [site](https://motifetmoi.com/assistant-ia) |
| **My Wed Paper Wedding Tools**<br>`com.mywedpaper/wedding-tools` | Wedding crosswords, newspaper templates, and printing guidance in English and French. | 1.0.0 | [site](https://mywedpaper.com/mcp/) |
| **Neoavis**<br>`com.neoavis/constats` | Can an AI agent buy on this French store? 500 stores tested: score, guest checkout, UCP. Free. | 1.3.0 | [site](https://neoavis.com/en/agents) |
| **Padel Expert Lab**<br>`com.padelexpertlab/raquettes` | Padel racket comparator for France and Belgium, in French: search, brand specs, profile-based picks. | 1.0.1 | [dépôt](https://github.com/FluxCoreGroup/padelexpertlab-mcp) · [site](https://padelexpertlab.com/connecteur-ia/) |
| **Paw Multiverse pet food recalls**<br>`com.pawmultiverse/recalls` | Pet food recalls from US, UK, Canadian and French registers, kept after removal. | 1.0.0 | [site](https://pawmultiverse.com/developers/) |
| **PV Solaire Énergie — Estimation solaire et devis (France)**<br>`com.pv-solaire-energie/devis-solaire` | Rooftop solar potential, RGE installer search and quote requests. France only, no API key. | 1.1.0 | [site](https://pv-solaire-energie.com/mcp) |
| `com.resadock/resadock` | Search and book local services in France: real availability, quotes, consented one-tap bookings. | 0.1.0 |  |
| **SMS Vert Pro**<br>`com.smsvertpro/mcp` | French SMS platform for AI agents: bulk &amp; scheduled SMS, OTP, delivery reports, replies, STOP list. | 1.1.0 | [site](https://www.smsvertpro.com/serveur-mcp-sms-heberge.php) |
| **Sooveryn**<br>`com.sooveryn/sooveryn` | AI personas with a lasting, encrypted memory per project, for Claude Code. Hosted in France. | 1.0.1 | [dépôt](https://github.com/PandaProgParis/sooveryn-mcp) · [site](https://www.sooveryn.com/) |
| **Super Webmaster**<br>`com.super-webmaster/seo` | Free SEO and GEO audit of any public website: scores out of 100, fixes ranked by gain. In French. | 1.2.0 | [site](https://super-webmaster.com/mcp-seo.php) |
| **WeekSpots**<br>`com.weekspots/events` | Upcoming events and outings in Paris and Île-de-France, plus permanent places across France. | 0.2.0 | [site](https://app.weekspots.com/data) |
| `com.whensport/tour-de-france-2026` | Tour de France 2026 MCP — 21 stages, race phase tracking, stage start times, stage profiles. | 1.0.1 | [site](https://whensport.com/events/tour-de-france-2026/) |
| **WPFormation : WordPress en français**<br>`com.wpformation/wordpress` | Articles WordPress en français et outils d'analyse de sites, de domaines et de plugins. | 0.5.0 | [site](https://wpformation.com/mcp-wpformation/) |
| **polls.karbach.digital**<br>`digital.karbach.polls/elections` | Election data for agents: Germany, Spain, France, EU. Polls, forecasts, MPs, energy. Paid via x402. | 1.1.0 | [site](https://polls.karbach.digital/api.html) |
| **Aztec Energy**<br>`energy.aztec/aztec-energy` | Dimensionnement de pompe à chaleur sur la consommation réelle, estimation des aides (France) | 1.0.0 | [site](https://aztec.energy/assistants-ia) |
| **Stream Estate**<br>`estate.stream/mcp` | French real estate listings, market data and property alerts (Stream Estate API v2). | 2.0.1 | [site](https://next.docs.stream.estate/en/docs/guides/mcp) |
| **Synapze — Financial Intermediary MCP**<br>`eu.synapze/financial-intermediary-mcp` | Connect AI agents to licensed financial intermediaries in France: insurance, credit, wealth. | 1.0.1 | [dépôt](https://github.com/ycdvll/synapze-mcp) · [site](https://mcp.synapze.eu) |
| **Apparais**<br>`fr.apparais.www/apparais` | Les commerces locaux que ChatGPT recommande en France, par métier et par ville. Test gratuit. | 1.0.1 | [dépôt](https://github.com/adam-thix/apparais-mcp) · [site](https://www.apparais.fr/mcp-apparais/) |
| **Boncourant**<br>`fr.boncourant/boncourant` | Audit et choix du tarif d'électricité en France : base \| hp\_hc \| tempo, contrats, puissance et PPA. | 1.6.0 | [dépôt](https://github.com/Amrit-Gill/boncourant-mcp) · [site](https://boncourant.fr/agents) |
| **CalculPro — outils de calcul du bâtiment**<br>`fr.calculpro/outils-batiment` | Calculs du bâtiment (France) : chute de tension NF C 15-100 et catalogue de 111 outils. | 1.0.0 | [site](https://calculpro.fr/mcp) |
| **Simulateurs salaire et freelance (France)**<br>`fr.calculsalairebrutnet/simulateurs` | Salaire brut et net, indemnités, chômage, APL, freelance : barèmes français 2026, sans inscription. | 1.1.0 | [dépôt](https://github.com/pwnera/simulateurs-fr) · [site](https://www.calculsalairebrutnet.fr/integrer/ia) |
| **CaLesVaut**<br>`fr.calesvaut/calesvaut` | Is this listing too expensive? French property sales (DVF), crime, rents, risks, schools | 1.0.0 | [site](https://calesvaut.fr/assistants) |
| **Coupondroid France**<br>`fr.coupondroid/public` | Live coupons, promo codes and deals from online shops in France. Read-only, no login. | 1.0.0 | [site](https://coupondroid.fr/) |
| **Coupure-Elec.fr**<br>`fr.coupure-elec.www/coupures` | Coupures électriques signalées en France en temps réel : état par ville, département, stats. | 1.0.0 | [site](https://www.coupure-elec.fr/mcp) |
| **CT Web : bilan de site**<br>`fr.ctweb/check` | Free check-up of a public website: security, e-mail, speed, DNS, cookies, SEO. Findings in French. | 1.0.0 | [site](https://ctweb.fr/agents) |
| **DéclaClic : panneaux solaires**<br>`fr.declaclic/panneaux-solaires` | Déclaration préalable pour panneaux solaires en France : parcelle, PLU, monument historique, pièces. | 1.0.0 | [site](https://declaclic.fr/assistants-ia) |
| **DIKOO**<br>`fr.dikoo/dikoo` | Annuaire d'avocats en France, créneaux publics et lien de rendez-vous. Lecture seule. | 1.0.0 | [site](https://www.dikoo.fr/ia) |
| **Edifiable**<br>`fr.edifiable/edifiable` | French land-use plans (PLU): what a zone or a street allows, cited at the article. No key. | 0.1.0 | [site](https://edifiable.fr/api) |
| `fr.fixou/fixou` | Create and track quote requests to local French tradespeople (80 trades) on fixou.fr. | 1.0.0 | [site](https://fixou.fr/developpeurs) |
| **Flytel**<br>`fr.flytel/flytel` | Forfaits mobiles Flytel pour les frontaliers France-Suisse : offres, appels, données, pays. | 1.3.0 | [site](https://flytel.fr) |
| **Fonixia — Standardiste téléphonique IA (France)**<br>`fr.fonixia/agent-vocal` | Create, configure and manage an AI phone receptionist. Requires a Fonixia account. | 1.0.0 | [site](https://fonixia.fr/mcp) |
| **FridgeBase (France)**<br>`fr.fridgebase/frigos` | French fridge comparison, with EU energy-label data (EPREL) where registered. | 1.0.0 | [site](https://fridgebase.fr/) |
| **gluten-free.fr: catalogue, comparatifs et guides sans gluten**<br>`fr.gluten-free/catalogue` | French gluten-free guide: product catalog, comparisons, ingredient checks and free guides. | 2.0.0 | [site](https://gluten-free.fr) |
| **HeavenPC (France)**<br>`fr.heavenpc/baies-informatiques` | French server rack comparison: search, filter and compare the models in HeavenPC's catalogue. | 1.0.0 | [site](https://heavenpc.fr/) |
| `fr.hypotheques-en-ligne/hel` | Instant pricing for French land-registry documents (etat hypothecaire, deeds). Read-only, no auth. | 1.0.0 | [site](https://www.hypotheques-en-ligne.fr/connecteur-claude/) |
| **Ilicompta — experts-comptables en France**<br>`fr.ilicompta/cabinets` | Find French accounting firms by city and documented criteria, with sources and catalog limitations. | 1.2.0 | [site](https://ilicompta.fr/developers/connect) |
| **IP73**<br>`fr.ip73/ip73` | French tech news, exploited vulnerabilities (KEV), cloud offers, AI models, scam guide. Read-only. | 1.0.0 | [site](https://ip73.fr/agents/) |
| **Izifete — prestataires événementiels en France**<br>`fr.izifete/prestataires` | Prestataires événementiels vérifiés en France, par métier et ville — Izifete. | 1.0.0 | [site](https://izifete.fr) |
| **KitchenWatt (France)**<br>`fr.kitchenwatt/robots-de-cuisine` | French food processor comparison: search, filter and compare KitchenWatt's catalogue. | 1.0.0 | [site](https://kitchenwatt.fr/) |
| **Les bancs**<br>`fr.lesbancs/lesbancs` | A French address: its catchment schools, nursery to high school, and each school's results. No key. | 0.1.0 | [site](https://lesbancs.fr/api) |
| **Lobbydex**<br>`fr.lobbydex/lobbydex` | French-speaking FiveM (GTA RP) servers: live players, rankings, events, recruiting, GTA 6 projects. | 1.0.0 | [site](https://lobbydex.fr/developpeurs/) |
| **Ménage Parfait**<br>`fr.menageparfait/booking` | Quote and book home cleaning in Paris &amp; Île-de-France; request office-cleaning quotes. | 1.2.0 |  |
| **Mes Frais de Notaire**<br>`fr.mesfraisnotaire/frais-notaire` | Frais de notaire en France : DMTO par département, émoluments réglementés, CSI et débours. | 1.0.0 | [site](https://www.mesfraisnotaire.fr/mcp) |
| **MesLoisirs — Things to do in France**<br>`fr.mesloisirs/recherche` | Real things to do in France: activities, tours, spas, escape games. Read-only, no sign-in. | 1.0.0 | [site](https://mesloisirs.fr/agents) |
| **Mission Museaux**<br>`fr.missionmuseaux/annonces` | Lost &amp; found pets in France: stray dogs and cats, photo search, shelter adoptions, breeds, advice. | 1.6.0 | [site](https://missionmuseaux.fr/aide/assistants-ia) |
| **Mon Batch Cooking**<br>`fr.monbatchcooking/recettes` | Recettes de batch cooking en français : recherche, idées avec vos placards, sessions de cuisine. | 1.0.0 | [site](https://monbatchcooking.fr/assistant-ia) |
| **MonRdvKine**<br>`fr.monrdvkine/disponibilites` | Trouver un kinesitherapeute en France et ses creneaux en ligne pour une premiere seance. | 1.0.0 | [site](https://monrdvkine.fr) |
| **OhMyWind**<br>`fr.ohmywind/sailing-planner` | Sailing passage planner for any coast, with high-precision tides on the French Atlantic. | 1.11.0 | [dépôt](https://github.com/qdonnars/ohmywind) · [site](https://ohmywind.fr) |
| **Pareil au même**<br>`fr.pareil-au-meme/mcp` | Checks a name against French trademarks in force, by spelling and by sound. Free, no account. | 1.1.1 | [dépôt](https://github.com/samuelbelolo/pareil-au-meme-mcp) · [site](https://pareil-au-meme.fr/serveur-mcp) |
| **Partner Construction**<br>`fr.partner-construction/assurance-decennale` | Indicative decennial insurance quote for French building trades, by a licensed broker. | 1.0.0 | [site](https://www.partner-construction.fr) |
| **Petit Papa Secret**<br>`fr.petitpapasecret/petitpapasecret` | Tirage au sort Secret Santa en français, sans compte : un lien privé par participant. | 1.1.1 | [site](https://petitpapasecret.fr/ia) |
| **Projet Ariane — Orientation**<br>`fr.projet-ariane/ariane-orientation` | Orientation en France : métiers, formations, Parcoursup, écoles, aides, offres. Sources officielles. | 1.1.0 | [site](https://www.projet-ariane.fr) |
| **RemplirPDF**<br>`fr.remplirpdf/mcp-server` | French rental documents (leases, rent receipts, inventories) generated as filled PDFs by AI agents. | 1.1.0 | [dépôt](https://github.com/benjammartin/remplirpdf-mcp) · [site](https://remplirpdf.fr/mcp) |
| **RenooLab - artisans in France**<br>`fr.renoolab/mcp` | Find French tradespeople, plan multi-trade projects, submit moderated contacts, create profiles. | 1.4.0 | [dépôt](https://github.com/mehdimicra/renoolab-mcp) · [site](https://renoolab.fr/mcp/) |
| **Capucine — ScoreCredit**<br>`fr.scorecredit/capucine` | French mortgage tools: HCSF capacity, dated rates, usury caps, ORIAS 26000195. | 2.2.0 | [site](https://scorecredit.fr/mcp) |
| `fr.statis-tir/statistir` | Tools to manage french shooter and competitions | 1.0.0 |  |
| **Symphony**<br>`fr.symphony/symphony` | Plan meals around your diet and allergies, build a basket, and order Symphony meal kits in France. | 1.0.0 | [site](https://symphony.fr/connector) |
| **TenderWatch, marchés publics**<br>`fr.tenderwatch/marches-publics` | Appels d'offres publics français ouverts, procédure selon le montant, intérêts moratoires, lexique. | 1.0.0 | [site](https://tenderwatch.fr) |
| **Aides à la rénovation énergétique (France)**<br>`fr.visitech/aides-renovation` | Estime MaPrimeRénov' et les primes CEE d'un projet de rénovation en France, avec calcul et sources. | 1.1.0 | [site](https://visitech.fr/integrer#assistants) |
| **TRAME**<br>`fr.vlek/trame` | Construction labour-hour estimates with durations, weather and contract model (French) | 1.19.0 | [site](https://agents.vlek.fr/connecteur) |
| **WashEdge (France)**<br>`fr.washedge/machines-a-laver` | French washing machine comparison, with EU energy-label data (EPREL) where registered. | 1.0.0 | [site](https://washedge.fr/) |
| **WashyDish (France)**<br>`fr.washydish/lave-vaisselle` | French dishwasher comparison, with EU energy-label data (EPREL) where registered. | 1.0.0 | [site](https://washydish.fr/) |
| **WoodRoar (France)**<br>`fr.woodroar/tronconneuses` | French chainsaw comparison: search, filter and compare the models in WoodRoar's catalogue. | 1.0.0 | [site](https://woodroar.fr/) |
| **YardRider (France)**<br>`fr.yardrider/tondeuses` | French lawn mower comparison: search, filter and compare the models in YardRider's catalogue. | 1.0.0 | [site](https://yardrider.fr/) |
| **French family benefits (CNAF corpus)**<br>`io.arxo/cnaf` | French family-benefit models (CNAF corpus) as executable rules with sources; independent demo | 0.1.1 | [dépôt](https://github.com/arxohq/arxo-cnaf) · [site](https://french-benefits.law.arxo.io/) |
| `io.github.AmauryDvl/serpmantics` | French-first SEO: semantic content guides, scoring, E-E-A-T audits for Google, ChatGPT &amp; Claude. | 0.1.0 | [site](https://app.serpmantics.com/landing/mcp) |
| `io.github.Arenafriends/valerie-mcp` | French real estate AI: DVF transactions, internet coverage, market insights. Free + €5/month. | 1.0.0 | [dépôt](https://github.com/Arenafriends/valerie-mcp) |
| **TenderAPI — French &amp; EU public tenders**<br>`io.github.IDNSIDNS/tenderapi-mcp` | MCP server for French (BOAMP) + EU (TED) public procurement data via TenderAPI. | 0.10.1 | [dépôt](https://github.com/IDNSIDNS/tenderapi-mcp) · [site](https://tenderapi.fr/) |
| **Sentinelle oppositions FR**<br>`io.github.Marcoco27/sentinelle-oppositions` | French business sales and mergers (BODACC) per debtor, with the creditor objection deadline. | 0.1.0 | [dépôt](https://github.com/Marcoco27/sentinelle-oppositions-mcp) · [site](https://oppositions.amacunia.fr/) |
| **Sentinelle phyto FR**<br>`io.github.Marcoco27/sentinelle-phyto` | French pesticide register (Anses E-Phy): still sellable or usable, and until when. | 0.1.0 | [dépôt](https://github.com/Marcoco27/sentinelle-phyto-mcp) · [site](https://phyto.amacunia.fr/) |
| **Sentinelle syndic FR**<br>`io.github.Marcoco27/sentinelle-syndic` | French condominium registry: current syndic and mandate end date, by condo or by area. | 0.1.0 | [dépôt](https://github.com/Marcoco27/sentinelle-syndic-mcp) · [site](https://syndic.amacunia.fr/) |
| `io.github.NormiHQ/normi` | French property market data — 17M+ DVF sales, 22M+ DPE energy ratings, 20M+ building records. | 0.2.7 | [dépôt](https://github.com/NormiHQ/Normi) · [site](https://normi.fr/docs/mcp) |
| **LeBonFoin**<br>`io.github.POP24/lebonfoin-mcp` | First MCP for the European hemp &amp; CBD market: real French CBD catalogue, producers, FR/EN wiki | 1.7.0 | [dépôt](https://github.com/POP24/lebonfoin-mcp) |
| **eyeot ERP**<br>`io.github.Termi24/eyeot-erp` | ERP français avec serveur MCP natif : plus de 1 800 actions métier auto-générées via OpenAPI. | 1.1.1 | [dépôt](https://github.com/Termi24/ERP-by-Eyeot-Software) |
| **Fredenslund workshop tools**<br>`io.github.afredenslund/fredenslund-workshop-tools` | MFT/3 hole patterns (Shaper-ready SVG) and French cleat wall plans with cut lists. Free, unofficial. | 1.0.10 | [site](https://fredenslund.dk/mcp) |
| **Canada Weather**<br>`io.github.asheeran/canada-weather` | Official Canadian weather: forecasts, conditions, alerts and air quality in English and French. | 1.0.0 | [site](https://weather.sitesy.ca/) |
| **SAFTI France Real Estate Agents**<br>`io.github.corent1robert/safti-real-estate-agents` | SAFTI France independent real estate advisors with phones, emails, city and sector, by region | 1.0.0 |  |
| **DeepPVMapper**<br>`io.github.gabrielkasmi/deeppvmapper` | Open registry of 1.14M+ rooftop-solar detections across France, queryable by natural language. | 0.1.0 | [dépôt](https://github.com/gabrielkasmi/deeppvmapper) · [site](https://deeppvmapper.fr) |
| `io.github.gaelasao/building-permits-api` | Building permits from 18 US cities (NYC, LA, Chicago, Philadelphia...), Canada, Ireland, France. | 1.0.3 | [dépôt](https://github.com/gaelasao/apify-mcp-tools) |
| **PennyPilot**<br>`io.github.holco-apps/pennypilot` | AI copilot for French accounting firms on Pennylane. Monthly close memo in 8s, full general ledger. | 0.2.6 | [dépôt](https://github.com/holco-apps/pennypilot) · [site](https://apps.holco.co/mcp/pennylane) |
| `io.github.iQuesta/MCP-iQuesta` | Recherche d'offres de stage et d'alternance en France via le réseau iQuesta.com | 1.0.0 | [dépôt](https://github.com/iQuesta/MCP-iQuesta) |
| `io.github.itsryanarmani/french4pr` | French tests and schools that count for Canadian PR. | 1.0.0 | [dépôt](https://github.com/itsryanarmani/directory) |
| **Storied Collection Venues**<br>`io.github.jchauge/storied-mcp` | Search historic castles, manors and châteaux for exclusive-use rental in the UK, Ireland and France. | 1.1.0 | [dépôt](https://github.com/jchauge/storied-mcp) · [site](https://storiedcollection.com/) |
| `io.github.jmshinhwa/i18n-fr-ca-bill96-lint` | Finds the France-French and left-over English lines in your fr-CA locale file before an OQLF complai | 1.0.1 | [dépôt](https://github.com/jmshinhwa/readystack-themes) |
| **Kanibal Tools**<br>`io.github.kanibal-studio/kanibal-tools-mcp` | Local read-only tools: French telemarketing consent, GDPR offshore checklist, cost per kept meeting | 0.1.1 | [dépôt](https://github.com/kanibal-studio/kanibal-tools-mcp) |
| `io.github.mabari2505/api-sorties-mcp` | Search French events and things to do, built for AI agents — geo, city, date, category filters. | 0.1.1 |  |
| **French Grocery**<br>`io.github.maxime-desmarchelier/fr-grocery-mcp` | Search, compare and manage E.Leclerc Drive and Super U grocery carts without ordering. | 0.1.1 | [dépôt](https://github.com/maxime-desmarchelier/fr-grocery-mcp) · [site](https://github.com/maxime-desmarchelier/fr-grocery-mcp#usage) |
| **Fabling**<br>`io.github.maximepeabody/fabling` | Practice Spanish and French using saved vocabulary, grammar progress, and practice focus. | 1.0.2 | [site](https://fabling.app/connect/help) |
| `io.github.mrfentmen/hal-mcp` | HAL: search the French open archive for papers, authors and years - keyless. | 1.0.0 | [dépôt](https://github.com/mrfentmen/awesome-servers) |
| `io.github.mrfentmen/theses-fr-mcp` | theses.fr: French doctoral theses - search by MCP: real tools for real work. | 1.0.0 | [dépôt](https://github.com/mrfentmen/awesome-servers) |
| **Frigolog HACCP — French Food Safety Compliance**<br>`io.github.naimterrache-a11y/frigolog-haccp` | French HACCP for restaurants: temperatures, DLC, allergens, DDPP controls, live recalls. Sourced. | 3.0.1 | [dépôt](https://github.com/naimterrache-a11y/frigolog-mcp) · [site](https://frigolog.fr/?utm_source=claude&utm_medium=mcp&utm_campaign=mcp_directory) |
| **NLocale**<br>`io.github.nconcierge/nlocale` | Find sourced events, places, restaurants, stays and local guides in Aisne, France. Read-only. | 0.2.0 | [site](https://nlocale.fr) |
| **Panda Leads Tools**<br>`io.github.pandadad0214/panda-leads-tools` | Public leads from official data: NPI, local, hiring, trucking, IRS, permits, new firms, France (15). | 0.2.0 | [dépôt](https://github.com/pandadad0214/panda-leads-tools) |
| `io.github.physics-star-cat/databutler-vehicles` | UK MOT failure data and official UK, French, Japanese and EU vehicle recalls. | 1.0.0 | [dépôt](https://github.com/physics-star-cat/databutler) · [site](https://databutler.dev/api) |
| **Amf Filings**<br>`io.github.pipeworx-io/amf-filings` | France AMF Regulated Filings MCP — keyless, same-day-fresh. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-amf-filings) · [site](https://pipeworx.io/packs/amf-filings) |
| **Bodacc**<br>`io.github.pipeworx-io/bodacc` | BODACC MCP — French official business-events bulletin (keyless). | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-bodacc) · [site](https://pipeworx.io/packs/bodacc) |
| **Fr Judilibre**<br>`io.github.pipeworx-io/fr-judilibre` | French court decisions (Cour de cassation and the judicial courts under it) — search, read a… | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-fr-judilibre) · [site](https://pipeworx.io/packs/fr-judilibre) |
| **France Bdpm Medicines**<br>`io.github.pipeworx-io/france-bdpm-medicines` | france-bdpm-medicines — France's official public medicines database (BDPM: 'Base de Donnees… | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-france-bdpm-medicines) · [site](https://pipeworx.io/packs/france-bdpm-medicines) |
| **France Boamp**<br>`io.github.pipeworx-io/france-boamp` | France BOAMP MCP — French government public procurement notices (keyless). | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-france-boamp) · [site](https://pipeworx.io/packs/france-boamp) |
| **France Geodata**<br>`io.github.pipeworx-io/france-geodata` | France Geodata MCP — point/radius questions on French public geodata. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-france-geodata) · [site](https://pipeworx.io/packs/france-geodata) |
| **Hal Fr**<br>`io.github.pipeworx-io/hal-fr` | HAL (Hyper Articles en Ligne) MCP — French national open research archive. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-hal-fr) · [site](https://pipeworx.io/packs/hal-fr) |
| **Idf Events**<br>`io.github.pipeworx-io/idf-events` | Île-de-France (Greater Paris) Events MCP. | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-idf-events) · [site](https://pipeworx.io/packs/idf-events) |
| **Nosdeputes Fr**<br>`io.github.pipeworx-io/nosdeputes-fr` | NosDéputés.fr MCP — civic-tech mirror of the French Assemblée nationale | 0.2.1 | [dépôt](https://github.com/pipeworx-io/mcp-nosdeputes-fr) · [site](https://pipeworx.io/packs/nosdeputes-fr) |
| `io.github.pooriaarab/french4pr` | French tests and schools that count for Canadian PR. | 1.0.0 | [dépôt](https://github.com/pooriaarab/directory) |
| **MemoraEU**<br>`io.github.pquattro/memoraeu` | Zero-knowledge persistent memory layer for AI agents. Encrypted client-side, hosted in France. | 0.1.6 | [dépôt](https://github.com/pquattro/memoraEu) · [site](https://memoraeu.com) |
| **DeepSeek FR MCP**<br>`io.github.rocnubie/deepseekfr-mcp` | Independent French workspace for evaluating DeepSeek-V3-class chat and DeepSeek-R1 reasoning. | 0.1.0 | [dépôt](https://github.com/rocnubie/deepseekfr-mcp) · [site](https://deepseekfr.org) |
| **DataRespublica**<br>`io.github.rodriguem75/datarespublica` | Independent platform exposing French official public territorial data through read-only MCP tools. | 1.1.1 | [site](https://www.datarespublica.com) |
| **GridLabs**<br>`io.github.rodriguem75/gridlabs` | French energy data, electricity and gas consumption, tariff scenarios and public discovery tools. | 1.0.1 | [site](https://www.gridlabs.fr) |
| **TelecomLabs**<br>`io.github.rodriguem75/telecomlabs` | French departmental 5G data, department comparison and dataset discovery. | 1.0.1 | [site](https://www.telecomlabs.fr) |
| `io.github.smeet666/mcp-bideetmusique` | Search the Bide &amp; Musique collection of forgotten French songs. Records, lyrics, no API key. | 2.1.0 | [dépôt](https://github.com/smeet666/mcp-bideetmusique) · [site](https://github.com/smeet666/mcp-bideetmusique#readme) |
| `io.github.smeet666/mcp-recipes` | Search six recipe sites at once, in French, English and Spanish, and scale quantities. | 4.1.0 | [dépôt](https://github.com/smeet666/mcp-recipes) · [site](https://github.com/smeet666/mcp-recipes#readme) |
| **Cachet**<br>`io.github.taraxe/cachet` | Send real paper mail in France from your agent: letters, tracked and registered letters with AR. | 1.0.0 | [site](https://www.taraxe.com/cachet/) |
| `io.github.waddou/mcp-compteparticulier` | Guides de connexion aux espaces clients français de compteparticulier.com (MCP, lecture seule). | 1.0.0 | [dépôt](https://github.com/waddou/mcp-compteparticulier) · [site](https://compteparticulier.com) |
| **IA-Asso.fr — French Associations Registry**<br>`io.github.wellknownmcp/ia-asso` | Search 1.26M+ active French associations (official RNA registry, 10 tools, premium &amp; RUP filters) | 1.0.0 | [dépôt](https://github.com/wellknownmcp/ia-asso.fr) · [site](https://ia-asso.fr) |
| **Le Scribe Audio — Transcription Audio &amp; Vidéo (FR)**<br>`io.github.zoulouzaza76/lescribeaudio-mcp` | Official Le Scribe Audio MCP server: search, read, summarize &amp; code French audio transcripts. | 1.0.0 | [dépôt](https://github.com/zoulouzaza76/lescribeaudio-mcp) · [site](https://lescribeaudio.com/integrations-mcp) |
| **Pylote: Freelance Tech Missions (France)**<br>`io.pylote/mcp` | Search freelance tech missions in France, see which recruiters viewed your profile, update it. | 1.0.0 | [site](https://pylote.io/mcp) |
| `io.toolzy/toolzy` | Recherche et location d'outils entre particuliers en France via Toolzy. | 0.1.0 | [site](https://www.toolzy.io) |
| **AwesomeFor.ms**<br>`ms.awesomefor/forms` | Create, publish and read online forms hosted in France, from your AI assistant. | 1.0.0 | [site](https://awesomefor.ms/mcp-server) |
| **Lampions — scrutins publics français**<br>`org.lampions/scrutins` | Décompte officiel des scrutins de l'Assemblée nationale, du Sénat et du Parlement européen. | 1.0.0 | [site](https://lampions.org) |
| **FindADoctor.si France**<br>`si.findadoctor.api-fr/fr-doctor-finder` | Find doctors in France by condition, procedure or place, with fee category and sources. No sign-up. | fr-2026-10-16.1 | [site](https://findadoctor.si/fr/) |
| **FindADoctor.si**<br>`si.findadoctor.api/us-doctor-finder` | Find US, New Zealand and France doctors by condition, procedure or place, with sources. No sign-up. | 2026-10-16.1 | [site](https://findadoctor.si/) |

<!-- liste-serveurs:fin -->

## Installation

Rien à installer pour lire les données : les fichiers se téléchargent directement depuis GitHub.

| Fichier | Adresse directe |
|---|---|
| Liste (JSON) | `https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/serveurs.json` |
| Liste (CSV) | `https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/serveurs.csv` |
| Historique (CSV) | `https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/historique.csv` |
| Source (JSON) | `https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/source.json` |

Pour figer une version, remplacer `main` par une étiquette, par exemple `v0.1.0`.

## Démarrage rapide

```php
<?php

declare(strict_types=1);

// Liste publiée sur GitHub (ou copie locale passée en argument).
$adresse = $argv[1] ?? 'https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/serveurs.json';
/** @var array{nombre: int, date_source: string, nombres: array<string, int>, serveurs: list<array{nom: string, titre: ?string, categorie: string, distant: list<array{type: string, url: string}>}>} $liste */
$liste = json_decode((string) file_get_contents($adresse), true, 16, JSON_THROW_ON_ERROR);

printf("%d serveurs MCP liés à la France, liste du %s\n", $liste['nombre'], $liste['date_source']);

// Les serveurs qui s'utilisent à distance (sans installation), avec leur adresse.
foreach ($liste['serveurs'] as $serveur) {
    foreach ($serveur['distant'] as $acces) {
        printf("%s (%s) : %s\n", $serveur['titre'] ?? $serveur['nom'], $serveur['categorie'], $acces['url']);
    }
}
```

Sortie attendue (avec une liste d'exemple) :

```text
2 serveurs MCP liés à la France, liste du 2026-11-16
Exemple Annuaire (entreprises_registres) : https://mcp.example.com/annuaire
```

## Utilisation

**En ligne de commande**, avec `curl` et `jq` : les noms des serveurs d'une catégorie.

```bash
curl -s https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/serveurs.json \
  | jq -r '.serveurs[] | select(.categorie == "donnees_publiques") | .nom'
```

**Dans Excel** : ouvrir directement `data/serveurs.csv` (UTF-8 avec BOM, séparateur point-virgule).

**Suivre les changements** : `data/historique.csv` ne fait que s'allonger. Les dernières lignes disent ce qui a
changé depuis votre dernière lecture.

```bash
curl -s https://raw.githubusercontent.com/polygonlab-ai/serveurs-mcp-france/main/data/historique.csv | tail -n 20
```

**Être prévenu** : sur GitHub, « Watch », puis « Custom » et « Releases » : chaque changement de la liste
donne une version.

**Demander un ajout ou un retrait** : ouvrir un ticket (« Issues ») en donnant le nom exact du serveur dans le
registre. Un serveur absent doit d'abord être publié dans le registre MCP officiel, avec une description qui dit
en quoi il concerne la France : il entrera alors seul dans la liste au passage suivant. Un serveur sans lien réel
avec la France est retiré par la liste d'exclusion, avec la raison et la date.

## Règles de sélection

1. **Source** : la liste complète du registre, dernière version de chaque serveur (`version=latest`), parcourue
   page par page. Un serveur présent plusieurs fois garde l'entrée marquée « dernière version », sinon la plus
   récemment mise à jour.
2. **Statut** : seuls les serveurs au statut `active` sont retenus (ni `deprecated`, ni `deleted`).
3. **Mots-clés** (`tools/mots-cles.json`) : au moins un mot-clé dans le nom, le titre ou la description, cherché
   sans tenir compte des majuscules ni des accents, en mot entier (« france » ne trouve pas « frances »).
   Liste actuelle : `france`, `french`, `francais`, `français`, `siren`, `siret`, `sirene`, `insee`, `data.gouv`,
   `datagouv`, `gouv.fr`, `etalab`, `legifrance`, `urssaf`, `impots.gouv`, `dgfip`, `france travail`,
   `pole emploi`, `ameli`, `sncf`, `ign`, `geoportail`, `api entreprise`, `annuaire entreprises`, `pappers`,
   `societe.com`, `infogreffe`, `inpi`, `chorus pro`, `facture electronique`, `factur-x`.
4. **Liste d'exclusion** (`tools/exclusions.json`) : serveurs retirés à la main (par exemple un serveur qui parle
   du « Tour de France » sans rien apporter sur la France), chacun avec sa raison et sa date.
5. **Écartés automatiquement** : un serveur dont une valeur publiée contient une adresse IPv4, une adresse e-mail
   (hors description), un motif de clé d'API ou de jeton, un texte entre doubles accolades, ou un mot de la liste
   de mots interdits tenue par PolygonLab. Il est noté « écarté » dans l'historique ; si c'est son nom qui pose
   problème, il y figure sous la forme « [nom retiré xxxxxxxx] ».
6. **Adresses e-mail** : une adresse trouvée dans une description est remplacée par « [adresse retirée] ».
   Aucune adresse e-mail n'est publiée.

Les valeurs reprises du registre ne sont pas modifiées, sauf les espaces en trop retirés et les adresses e-mail
remplacées. Les dates de publication et de mise à jour sont données au jour (heure de Paris).

## Catégories

Attribuées par mots-clés (`tools/categories.json`) : la première catégorie de la liste dont un mot-clé figure
dans le nom, le titre ou la description l'emporte.

| Clé | Catégorie | Exemples de mots-clés |
|---|---|---|
| `entreprises_registres` | Entreprises et registres | siren, siret, sirene, pappers, infogreffe, inpi, entreprise |
| `emploi_social` | Emploi et social | france travail, pole emploi, urssaf, emploi, job |
| `sante` | Santé | ameli, santé, health, médicament, hôpital |
| `juridique_administration` | Juridique et administration | legifrance, droit, legal, jurisprudence, service public |
| `fiscalite_facturation` | Fiscalité et facturation | impots.gouv, dgfip, tva, facture électronique, factur-x, chorus pro |
| `transport_mobilite` | Transport et mobilité | sncf, train, transport, vélo, logistics, colis |
| `geographie_territoire` | Géographie et territoire | ign, geoportail, cadastre, adresse, commune, carte |
| `donnees_publiques` | Données publiques | data.gouv, gouv.fr, etalab, insee, open data |
| `commerce_paiement` | Commerce et paiement | commerce, shop, paiement, banque, prix |
| `autre` | Autre | aucun mot-clé : tout le reste |

## Format des fichiers

### `data/serveurs.json`

Schéma : [`schema/serveurs.schema.json`](schema/serveurs.schema.json).

| Champ | Type | Contenu |
|---|---|---|
| `source` | texte | « Registre MCP officiel, registry.modelcontextprotocol.io » |
| `page` | texte | Page du registre |
| `date_source` | date ou null | Date de la collecte (`AAAA-MM-JJ`, heure de Paris) |
| `nombre` | entier | Nombre total de serveurs |
| `nombres` | objet | Nombre de serveurs par catégorie |
| `serveurs` | liste | Serveurs, triés par nom |

Chaque serveur :

| Champ | Type | Contenu |
|---|---|---|
| `nom` | texte | Nom du serveur dans le registre, unique (par exemple `io.github.exemple/serveur-mcp`) |
| `titre` | texte ou null | Titre lisible donné par l'éditeur |
| `description` | texte ou null | Description donnée par l'éditeur |
| `version` | texte | Dernière version publiée dans le registre |
| `categorie` | texte | Clé de la catégorie (voir « Catégories ») |
| `depot` | texte ou null | Adresse du dépôt de code source |
| `site` | texte ou null | Site web |
| `distant` | liste | Points d'accès distants : `type` (par exemple `streamable-http`) et `url` ; vide si le serveur s'installe en local |
| `paquets` | liste | Paquets d'installation : `registre` (par exemple `npm`, `pypi`, `oci`) et `identifiant` ; vide si aucun |
| `publie_le` | date ou null | Première publication dans le registre |
| `mis_a_jour_le` | date ou null | Dernière mise à jour dans le registre |

### `data/serveurs.csv`

Les mêmes serveurs, un par ligne, dans le même ordre : colonnes `nom`, `titre`, `description`, `version`,
`categorie`, `depot`, `site`, `distant`, `paquets`, `publie_le`, `mis_a_jour_le`. Les listes sont aplaties
(« type adresse », séparés par « | »). UTF-8 avec BOM, séparateur `;`, valeurs entre guillemets seulement si
nécessaire.

### `data/historique.csv`

Une ligne par événement : `date` (jour de la collecte), `evenement` (`entree`, `sortie`, `nouvelle_version`,
`changement_categorie`, `ecarte`), `nom`, `categorie`, `detail`. Le fichier ne fait que s'allonger.

### `data/source.json`

Adresse lue, date de la collecte (`lu_le`) et date de la liste (`date_source`), nombre de pages et d'entrées
lues, nombre de serveurs actifs et retenus, et l'empreinte SHA-256 des données normalisées de la collecte (serveurs
retenus, triés par nom, et règles de sélection) : une page qui change d'ordre sans changer le contenu ne donne
pas de nouvelle version.

## Configuration

L'outil d'actualisation refait la liste à partir du registre, sans aucune bibliothèque à installer :

```bash
php tools/actualiser.php
```

| Option | Type | Valeur par défaut | Rôle |
|---|---|---|---|
| `--collecter <dossier>` | chemin | aucun | Collecte seule : fichier normalisé écrit dans ce dossier (avec `--manifeste`) |
| `--manifeste <fichier.json>` | chemin | aucun | Avec `--collecter` : manifeste à écrire ; seul : collecte déjà faite à utiliser |
| `--donnees <dossier>` | chemin | `data/` | Dossier des données à mettre à jour |
| `--readme <fichier.md>` | chemin | `README.md` | README dont le tableau est réécrit |
| `--resume <fichier.json>` | chemin | aucun | Résumé des changements, en JSON |
| `--mots-interdits <fichier.json>` | chemin | aucun | Mots qui écartent un serveur : `{"mots": [{"mot": "…", "tolerees": ["…"]}]}` |
| `--pages <dossier>` | chemin | aucun | Rejoue une collecte enregistrée (`page-0001.json`…), sans réseau |
| `--enregistrer <dossier>` | chemin | aucun | Enregistre les pages lues, pour les rejouer avec `--pages` |
| `--chargeur <fichier.php>` | chemin | `vendor/autoload.php` | Chargeur automatique à utiliser à la place de celui de Composer |

`tools/sources.json` règle la collecte :

| Clé | Valeur | Rôle |
|---|---|---|
| `registre` | `https://registry.modelcontextprotocol.io/v0/servers` | API lue |
| `par_page` | 100 | Serveurs par page |
| `pages_max` | 2000 | Plafond de pages : atteint, la collecte s'arrête sans rien écrire |
| `pause_s` | 1 | Pause entre deux pages (secondes) |
| `essais` | 3 | Essais par page (pauses de 5 puis 15 secondes) |
| `delai_s` | 60 | Délai d'une page (secondes) |
| `taille_max_page` | 10000000 | Taille maximale d'une page (octets) |
| `duree_max_s` | 2400 | Durée maximale de la collecte (secondes) |

L'outil s'arrête sans rien écrire devant une page illisible, une entrée sans `server.name`, `server.version` ou
statut officiel (entrée citée), un plafond ou une durée dépassés, ou une liste vide. Les autres champs sont
facultatifs : absents ou de forme inattendue, ils sont ignorés.

## Limites

- **Seul le registre MCP officiel fait foi.** Ce dépôt en est une copie partielle, lue chaque semaine.
- La sélection repose sur des mots-clés : un serveur lié à la France qui n'en emploie aucun n'y est pas, et un
  serveur sans lien réel peut y entrer tant qu'il n'est pas exclu.
- Les catégories sont attribuées automatiquement, par mots-clés : elles peuvent se tromper.
- Ce dépôt ne dit rien de la qualité, de la sécurité ni du sérieux d'un serveur : il recopie le registre, sans
  avis. Vérifiez un serveur avant de lui confier des données.

## Source et licence des données

Source : [Registre MCP officiel](https://registry.modelcontextprotocol.io), registry.modelcontextprotocol.io. Les
descriptions et les autres valeurs sont celles que leurs éditeurs ont publiées dans le registre ; la date de la
collecte est donnée par `date_source` et dans `data/source.json`.

Le code (outil, schéma, tests) est sous licence MIT.

## Compatibilité

Outil d'actualisation : PHP 8.2, 8.3 et 8.4 (extensions curl, json et mbstring, aucune autre bibliothèque),
testés par l'intégration continue. Les données se lisent avec n'importe quel langage.

## Tests

```bash
composer install
composer test
```

Les tests utilisent de fausses réponses du registre, sans appel réseau, et vérifient que `data/` respecte le
schéma.

## Contribuer

Voir [CONTRIBUTING.md](https://github.com/polygonlab-ai/.github/blob/main/CONTRIBUTING.md).

## Sécurité

Une faille se signale en privé, jamais dans un ticket public : voir
[SECURITY.md](https://github.com/polygonlab-ai/.github/blob/main/SECURITY.md).

## Licence

Code sous licence MIT : voir [LICENSE](LICENSE). Données : voir « Source et licence des données ».

---
— Conçu et développé par PolygonLab · polygonlab.ai · Licence MIT
