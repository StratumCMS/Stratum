# Stratum CMS

**Stratum** est un CMS universel, modulaire, et open source, développé avec **Laravel 10**. Conçu pour répondre aux besoins variés des utilisateurs (entreprises, particuliers, institutions, développeurs), Stratum se distingue par sa légèreté, sa flexibilité, et son optimisation.

Avec un système avancé de **thèmes** et **modules**, Stratum permet de créer des sites web vitrines, portfolios, blogs, e-commerces, et bien plus, tout en restant facile à utiliser et à personnaliser.

---

## 🚀 **Fonctionnalités principales**

- **Modularité complète** : Activez ou désactivez les fonctionnalités dont vous avez besoin via des plugins.
- **Thèmes personnalisables** : Support des thèmes en Blade avec compatibilité pour les frameworks CSS de votre choix (ex : TailwindCSS, Bootstrap).
- **Performances optimales** : CMS léger et rapide, idéal pour tout type de projet.
- **Administration intuitive** : Interface responsive pour gérer facilement contenus, utilisateurs, thèmes, et modules.
- **Open Source** : Gratuit et disponible pour tous, avec des extensions premium (thèmes et modules).

---

## 🛠️ **Installation**

### **Prérequis**
- **PHP** : 8.1 ou supérieur
- **Composer**
- **Node.js** (pour le build frontend)
- **Base de données** : MySQL/MariaDB, PostgreSQL ou SQLite

### **Étapes**
1. Clonez le projet :
   ```bash
   git clone https://github.com/Velyorix/Stratum.git
   cd stratum
   ```

2. Installez les dépendances PHP et JavaScript :
   ```bash
   composer install
   npm install
   ```

3. Configurez votre fichier `.env` :
   Copiez le fichier `.env.example` et configurez les informations nécessaires (base de données, clés d'API, etc.) :
   ```bash
   cp .env.example .env
   ```

4. Générez la clé d'application :
   ```bash
   php artisan key:generate
   ```

6. Compilez les assets frontend :
   ```bash
   npm run dev:admin && npm run dev:install && npm run dev:default
   ```

7. Lancez le serveur local :
   ```bash
   php artisan serve
   ```

Votre CMS est maintenant accessible à l’adresse [http://localhost:8000](http://localhost:8000).

---

## 🌟 **Fonctionnalités disponibles**

### **Modules intégrés**
- Authentification (gestion des utilisateurs et permissions).
- Blog (articles, catégories, commentaires).
- SEO (sitemaps, métadonnées, optimisation).
- Galerie d'images et gestionnaire de médias.

### **Thème par défaut**
- Design moderne et épuré, développé avec **TailwindCSS**.
- Mode clair avec optimisation pour les performances.

---

## 📚 **Documentation**

La documentation complète (installation, configuration, création de thèmes et modules) est disponible [ici](https://stratum-docs.velyorix.com).

---

## 🤝 **Contributions**

Les contributions sont les bienvenues ! Pour contribuer :
1. Forkez ce répo.
2. Créez une branche pour vos modifications :
   ```bash
   git checkout -b feature/ma-fonctionnalite
   ```
3. Faites une Pull Request.

Merci de consulter le fichier `CONTRIBUTING.md` pour plus d'informations.

---

## 🛡️ **Licence**

Stratum est distribué sous la licence MIT. Consultez le fichier [LICENSE](./LICENSE) pour plus d'informations.

---

## 💡 **Idées de modules/thèmes ?**
Si vous avez des idées pour des thèmes ou modules, ou si vous voulez contribuer à l’écosystème Stratum, contactez-nous via [contact@velyorix.com](mailto:contact@velyorix.com).
