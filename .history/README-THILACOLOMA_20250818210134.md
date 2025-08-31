# Thila Coloma - Statamic Laravel Website

Een moderne Statamic Laravel implementatie voor scoutsgroep Thila Coloma uit Mechelen. Deze website is gemigreerd van een Craft CMS installatie naar Statamic voor betere flexibiliteit en Laravel ecosystem voordelen.

## 🚀 Features

### Huidige Functionaliteiten
- **Statamic CMS**: Flat-file CMS met krachtige Laravel backend
- **Responsief Design**: Werkt perfect op desktop, tablet en mobiel
- **Dynamische Content**: Collections voor nieuws, pagina's en takken
- **Asset Management**: Georganiseerde bestandsbeheer 
- **DDEV Development**: Lokale ontwikkelomgeving met Docker
- **Antlers Templates**: Moderne templating engine

### Pagina's
1. **Home**: Nieuws slideshow en snelle navigatie
2. **Takken**: Overzicht alle scouts takken (Kapoenen → Voortrekkers)
3. **Nieuws**: Dynamische nieuwsberichten
4. **Kalender**: Evenementen en activiteiten
5. **Verhuur**: Lokalen en materiaal verhuur
6. **Over Ons**: Informatie over de scoutsgroep

## 📁 Project Structuur

```
thilacoloma-statamic/
├── app/                    # Laravel applicatie
├── config/                 # Laravel & Statamic configuratie
├── content/                # Statamic content (flat files)
│   ├── collections/        # Content collections
│   │   ├── pages/         # Algemene pagina's
│   │   ├── news/          # Nieuws artikelen
│   │   └── takken/        # Scouts takken
├── resources/
│   ├── blueprints/        # Statamic content blueprints
│   └── views/             # Antlers templates
├── public/
│   └── assets/            # CSS, JS, images (gekopieerd van Craft CMS)
├── .ddev/                 # DDEV ontwikkelomgeving
└── users/                 # Statamic gebruikers
```

## 🔧 Installatie & Setup

### 1. DDEV Development (Aanbevolen)

```bash
# Start DDEV environment
ddev start

# Install composer dependencies
ddev composer install

# Access website
# https://thilacoloma-statamic.ddev.site
```

### 2. Local Development (Alternatief)

```bash
# Install dependencies
composer install

# Start development server
php artisan serve --host=0.0.0.0 --port=8000

# Access website
# http://localhost:8000
```

## 🎯 Content Management

### Admin Panel Toegang
- **URL**: `/cp` (Control Panel)
- **Gebruiker**: coelho@thilacoloma.be
- **Functionaliteit**: 
  - Content beheer voor alle collections
  - Asset upload en management
  - Gebruikersbeheer
  - Site configuratie

### Content Collections

#### **Pages Collection**
- Algemene website pagina's
- URL structuur: `/{slug}`
- Blueprint: `resources/blueprints/collections/pages/page.yaml`

#### **News Collection**  
- Nieuwsberichten en evenementen
- URL structuur: `/nieuws/{slug}`
- Blueprint: `resources/blueprints/collections/news/news_article.yaml`
- Featured articles tonen in homepage slideshow

#### **Takken Collection**
- Scouts takken informatie
- URL structuur: `/takken/{slug}`
- Blueprint: `resources/blueprints/collections/takken/tak.yaml`
- Sorteerd op volgorde (Kapoenen → Voortrekkers)

## 🎨 Frontend Assets

De originele Craft CMS assets zijn volledig gekopieerd:
- **CSS**: `/public/assets/css/` - Modulaire opbouw behouden
- **JavaScript**: `/public/assets/js/` - Slideshow en interactieve elementen
- **Images**: `/public/assets/images/` - Georganiseerd per categorie
- **Documents**: `/public/assets/documents/` - Downloads en resources

## 📱 Templates

### Template Structuur
- **Layout**: `resources/views/layout.antlers.html` - Basis HTML structuur
- **Home**: `resources/views/home.antlers.html` - Homepage met slideshow
- **Default**: `resources/views/default.antlers.html` - Algemene pagina's
- **Takken**: `resources/views/takken/index.antlers.html` - Takken overzicht
- **Partials**: `resources/views/partials/` - Herbruikbare componenten

### Antlers Templating
Statamic gebruikt Antlers templating engine:
```twig
{{ collection:news limit="3" sort="date:desc" }}
    <h2>{{ title }}</h2>
    <p>{{ excerpt }}</p>
{{ /collection:news }}
```

## 🔄 Migratie van Craft CMS

### Wat is gemigreerd:
✅ **Alle assets** - CSS, JS, images volledig gekopieerd
✅ **Template design** - Van Twig naar Antlers geconverteerd  
✅ **Content structuur** - Pages, nieuws, takken als collections
✅ **Navigatie** - Volledig werkende navigatie behouden
✅ **Slideshow functionaliteit** - JavaScript en CSS intact

### Voordelen van Statamic:
- 🚀 **Laravel ecosystem** - Gebruik van alle Laravel features
- 📝 **Flat-file content** - Geen database nodig voor content
- 🎨 **Flexibele templates** - Antlers templating engine
- 🔧 **Developer friendly** - Git-vriendelijke content opslag
- 📱 **Modern admin panel** - Intuïtieve interface

## 🚀 Development

### Content toevoegen
1. **Via Control Panel**: Ga naar `/cp` → Collections
2. **Via bestanden**: Voeg `.md` bestanden toe in `content/collections/`

### Templates aanpassen
1. **Antlers templates**: `resources/views/`
2. **CSS/JS**: `public/assets/`
3. **Live reload**: DDEV ondersteunt hot reloading

### Assets beheren
- **Upload via CP**: Assets → Upload naar juiste folders
- **Direct toegang**: `public/assets/` directory

## 📞 Support & Documentatie

- **Statamic Docs**: https://statamic.dev/
- **Laravel Docs**: https://laravel.com/docs
- **Antlers Guide**: https://statamic.dev/antlers

## 🔐 Security

- **Admin toegang**: Alleen via `/cp` met geldig account
- **File permissions**: Standaard Laravel security
- **Environment**: `.env` configuratie voor verschillende omgevingen

---

**🏆 Resultaat**: Een moderne, flexibele Statamic Laravel website voor Thila Coloma met alle originele functionaliteiten en verbeterde content management!
