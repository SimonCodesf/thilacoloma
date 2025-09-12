# Auto Git Sync voor Statamic - Thila Coloma

## 🎯 Overzicht

Dit systeem zorgt ervoor dat **alle wijzigingen** die Antilope of andere gebruikers maken in het Statamic beheerpaneel **automatisch worden gesynchroniseerd naar GitHub**. Geen handmatig werk meer - elke save van een pagina, nieuws artikel, of andere content wordt direct gepusht naar de repository.

## 🚀 Hoe het werkt

### 1. **Statamic Event Listeners**
Wanneer iemand content opslaat in Statamic:
- `EntrySaved` - Pagina of nieuws artikel opgeslagen
- `EntryDeleted` - Content verwijderd  
- `GlobalSaved` - Globale instellingen gewijzigd
- `AssetSaved` - Afbeelding of bestand geüpload

### 2. **Automatische Webhook Trigger**
Het systeem stuurt automatisch een verzoek naar `/auto-git-sync.php` met:
- Wie de wijziging heeft gemaakt (email van gebruiker)
- Wat er is gewijzigd (collectie, slug, titel)
- Wanneer de wijziging is gemaakt

### 3. **Git Synchronisatie** 
Het sync script:
- ✅ Controleert of er wijzigingen zijn
- ✅ Maakt een commit met duidelijke beschrijving
- ✅ Pusht naar GitHub 
- ✅ Logt alles voor monitoring
- ✅ Voorkomt conflicten met lock systeem

## 📁 Bestanden

```
/auto-git-sync.php                    # Hoofd sync script
/app/Listeners/AutoGitSyncListener.php # Statamic event listeners
/app/Providers/EventServiceProvider.php # Geregistreerde listeners
/public/auto-git-sync.php             # Web toegankelijke webhook
/public/git-sync-status.php           # Status dashboard
/.env                                 # Configuratie (WEBHOOK_TOKEN)
/config/app.php                       # App configuratie
```

## ⚙️ Configuratie

### Environment Variables (.env):
```env
WEBHOOK_TOKEN=thila-coloma-auto-sync-2025
GIT_SYNC_ENABLED=true
GIT_SYNC_WEBHOOK_URL=http://103.76.86.167/auto-git-sync.php
```

### Veiligheid:
- 🔒 Webhook token authenticatie
- 🚫 Lock systeem voorkomt dubbele executions
- 📝 Complete logging van alle acties
- ⏱️ Timeout protectie

## 🎛️ Monitoring & Beheer

### Status Dashboard:
Ga naar: `http://103.76.86.167/git-sync-status.php?token=thila-coloma-auto-sync-2025`

Je kunt:
- ✅ Sync status bekijken
- ✅ Logs lezen
- ✅ Handmatige sync triggeren
- ✅ Git changes monitoren

### Log Bestanden:
```bash
tail -f storage/logs/auto-git-sync.log    # Real-time logs
cat storage/logs/git-sync.lock            # Actieve lock check
```

## 🧪 Testen

### 1. Handmatige Test:
```bash
# Test het script direct
php auto-git-sync.php

# Test via webhook
curl -X POST "http://localhost/auto-git-sync.php?token=thila-coloma-auto-sync-2025&trigger=manual&user=test"
```

### 2. Content Test:
1. Log in op Statamic beheerpaneel
2. Maak wijziging aan een pagina of nieuws artikel
3. Sla op
4. Check GitHub repository - commit moet er automatisch staan

### 3. Status Test:
- Bezoek status dashboard
- Trigger handmatige sync
- Bekijk logs

## 🔧 Gebruik voor Antilope

**Antilope hoeft NIETS anders te doen!** 

Gewoon zoals altijd:
1. Log in op Statamic
2. Maak wijzigingen
3. Klik "Save" 

Het systeem doet de rest automatisch ⚡

## 📋 Commit Berichten

Commits krijgen automatisch duidelijke berichten:
```
[AUTO-SYNC] 2 modified: verhuur.md, stamhoofd.md (by antilope@thilacoloma.be at 2025-01-09 14:30:22)
[AUTO-SYNC] 1 added: nieuws/nieuwe-activiteit.md (by antilope@thilacoloma.be at 2025-01-09 14:31:15)  
[AUTO-SYNC] Global settings updated (by admin@thilacoloma.be at 2025-01-09 14:32:01)
```

## 🛠️ Troubleshooting

### Als sync niet werkt:
1. Check status dashboard voor errors
2. Bekijk logs: `storage/logs/auto-git-sync.log`
3. Controleer git status: `git status`
4. Test handmatige sync via dashboard

### Veelvoorkomende problemen:
- **Lock file blijft hangen**: Verwijder `storage/logs/git-sync.lock`
- **Permission errors**: Check file permissions
- **Git authentication**: Zorg dat Git credentials zijn ingesteld
- **Webhook bereikbaarheid**: Test webhook URL direct

## 🎉 Voordelen

✅ **Geen data verlies meer** - Alles wordt automatisch opgeslagen  
✅ **Real-time sync** - Wijzigingen zijn direct zichtbaar op GitHub  
✅ **Volledige transparantie** - Wie heeft wat gewijzigd en wanneer  
✅ **Geen extra werk** - Gebruikers hoeven niets anders te doen  
✅ **Betrouwbaar** - Lock systeem voorkomt conflicten  
✅ **Monitoring** - Status dashboard voor complete controle  

Het probleem van verloren wijzigingen is nu volledig opgelost! 🎯