<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Statamic\Facades\Entry;
use Statamic\Facades\Collection;

class LiveSearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('q', '');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }
        
        $results = [];
        $query = strtolower($query);
        
        // Get all published entries
        $entries = Entry::query()
            ->where('published', true)
            ->get();

        foreach ($entries as $entry) {
            $score = 0;
            $title = $entry->get('title', '');
            $slug = $entry->slug();
            $url = $entry->url();
            
            // Skip if no title or URL
            if (!$title || !$url) {
                continue;
            }

            // Get icon and description based on page type
            $icon = $this->getIconForEntry($entry);
            $description = $this->getDescriptionForEntry($entry);
            
            // Title exact match gets highest score
            if (stripos($title, $query) === 0) {
                $score += 100;
            } elseif (stripos($title, $query) !== false) {
                $score += 50;
            }
            
            // Slug match gets medium score
            if (stripos($slug, $query) !== false) {
                $score += 30;
            }

            // Extended content search - check multiple fields
            $searchableContent = [];
            
            // Basic content fields
            $searchableContent[] = $entry->get('intro_text', '');
            $searchableContent[] = $entry->get('content', '');
            $searchableContent[] = $entry->get('meta_description', '');
            
            // Page-specific fields based on blueprint/collection
            $collection = $entry->collection() ? $entry->collection()->handle() : '';
            
            switch ($collection) {
                case 'takken':
                    $searchableContent[] = $entry->get('leeftijd', '');
                    $searchableContent[] = $entry->get('beschrijving', '');
                    $searchableContent[] = $entry->get('activiteiten', '');
                    break;
                    
                case 'pages':
                    if ($slug === 'verhuur') {
                        $searchableContent[] = $entry->get('contact_persoon', '');
                        $searchableContent[] = $entry->get('lokalen_titel', '');
                        $searchableContent[] = $entry->get('materiaal_titel', '');
                        $searchableContent[] = $entry->get('tenten_titel', '');
                    } elseif ($slug === 'stamhoofd') {
                        $searchableContent[] = $entry->get('groepsleiding_titel', '');
                        $searchableContent[] = $entry->get('bestuur_titel', '');
                        $searchableContent[] = $entry->get('contact_titel', '');
                    } elseif ($slug === 'kalender') {
                        $searchableContent[] = $entry->get('evenementen_titel', '');
                        $searchableContent[] = $entry->get('agenda_titel', '');
                    }
                    break;
            }
            
            // Search in all content
            $allContent = implode(' ', $searchableContent);
            if (stripos($allContent, $query) !== false) {
                $score += 10;
            }

            // Search in sections for pages like verhuur
            $this->searchInSections($entry, $query, $results, $score, $icon);
            
            if ($score > 0) {
                $results[] = [
                    'title' => $title,
                    'url' => $url,
                    'score' => $score,
                    'type' => 'page',
                    'icon' => $icon,
                    'description' => $description
                ];
            }
        }

        // Search in people/leiding if query matches names
        $this->searchInPeople($query, $results);

        // Sort by score descending
        usort($results, function ($a, $b) {
            return $b['score'] - $a['score'];
        });

        // Return max 5 results
        return response()->json(array_slice($results, 0, 5));
    }

    private function getIconForEntry($entry)
    {
        $slug = $entry->slug();
        $title = strtolower($entry->get('title', ''));
        
        // Map specific pages to their exact header icons
        $iconMap = [
            'kalender' => '/assets/images/icons/Asset 12agenda.svg',
            'stamhoofd' => '/assets/images/icons/Asset 1stamhoofd.svg',
            'thilala' => '/assets/images/icons/Asset 11thilala.svg',
            'verhuur' => '/assets/images/icons/Asset 13verhuur.svg',
            'takken' => '/assets/images/icons/Asset 7takken.svg',
            'kapoenen' => 'fas fa-seedling',
            'welpen' => 'fas fa-paw',
            'jongverkenners' => 'fas fa-hiking',
            'voortrekkers' => 'fas fa-mountain',
        ];

        // Check slug first
        if (isset($iconMap[$slug])) {
            return $iconMap[$slug];
        }

        // Check title
        foreach ($iconMap as $key => $icon) {
            if (stripos($title, $key) !== false) {
                return $icon;
            }
        }

        // Default icon
        return 'fas fa-file-alt';
    }

    private function getDescriptionForEntry($entry)
    {
        $slug = $entry->slug();
        $collection = $entry->collection() ? $entry->collection()->handle() : '';
        
        // Add context for duplicate titles
        if ($collection === 'takken') {
            return 'Tak informatie';
        }
        
        return 'Pagina';
    }

    private function searchInSections($entry, $query, &$results, $baseScore, $baseIcon)
    {
        // Special handling for verhuur page - search in all material-related fields
        if ($entry->slug() === 'verhuur') {
            
            // Check for "materiaal" or "material" queries
            if (stripos($query, 'materiaal') !== false || stripos($query, 'material') !== false) {
                $results[] = [
                    'title' => $entry->get('materiaal_titel', 'Materiaal'),
                    'url' => $entry->url() . '#materiaal',
                    'score' => $baseScore + 90,
                    'type' => 'section',
                    'icon' => '/assets/images/icons/Asset 13verhuur.svg',
                    'description' => 'Verhuur - Materiaal sectie'
                ];
                return;
            }
            
            // Search in specific material categories
            $materialSections = [
                'materiaal_transport' => 'Transport',
                'materiaal_meubilair' => 'Meubilair', 
                'materiaal_kookbenodigdheden' => 'Kookbenodigdheden',
                'materiaal_koeling' => 'Koeling',
                'materiaal_gereedschap' => 'Gereedschap'
            ];
            
            foreach ($materialSections as $fieldName => $sectionTitle) {
                $sectionData = $entry->get($fieldName, []);
                foreach ($sectionData as $item) {
                    $itemName = $item['naam'] ?? '';
                    if (stripos($itemName, $query) !== false) {
                        $results[] = [
                            'title' => $itemName,
                            'url' => $entry->url() . '#materiaal',
                            'score' => $baseScore + 70,
                            'type' => 'section',
                            'icon' => '/assets/images/icons/Asset 13verhuur.svg',
                            'description' => 'Verhuur - ' . $sectionTitle . ' - ' . $itemName
                        ];
                    }
                }
            }
            
            // Search in general verhuur sections
            $sections = $entry->get('sections', []);
            foreach ($sections as $section) {
                $sectionTitle = $section['title'] ?? '';
                $sectionContent = $section['content'] ?? '';
                
                if (stripos($sectionTitle, $query) !== false || stripos($sectionContent, $query) !== false) {
                    $sectionScore = $baseScore;
                    if (stripos($sectionTitle, $query) !== false) {
                        $sectionScore += 60;
                    }
                    if (stripos($sectionContent, $query) !== false) {
                        $sectionScore += 20;
                    }
                    
                    $results[] = [
                        'title' => $sectionTitle ?: 'Verhuur sectie',
                        'url' => $entry->url() . '#' . str_slug($sectionTitle),
                        'score' => $sectionScore,
                        'type' => 'section',
                        'icon' => '/assets/images/icons/Asset 13verhuur.svg',
                        'description' => 'Verhuur - ' . $sectionTitle
                    ];
                }
            }
        }
    }

    private function searchInPeople($query, &$results)
    {
        // Search in stamhoofd page for leiding names
        $stamhoofEntry = Entry::query()
            ->where('slug', 'stamhoofd')
            ->where('published', true)
            ->first();
            
        if (!$stamhoofEntry) {
            return;
        }
        
        // Search in groepsleiding
        $groepsleiding = $stamhoofEntry->get('groepsleiding', []);
        foreach ($groepsleiding as $persoon) {
            $naam = $persoon['naam'] ?? '';
            $functie = $persoon['functie'] ?? '';
            $totem = $persoon['totem'] ?? '';
            
            if (stripos($naam, $query) !== false || 
                stripos($functie, $query) !== false || 
                stripos($totem, $query) !== false) {
                
                $results[] = [
                    'title' => $naam . ($totem ? " ($totem)" : ''),
                    'url' => $stamhoofEntry->url() . '#groepsleiding',
                    'score' => 80,
                    'type' => 'person',
                    'icon' => '/assets/images/icons/Asset 1stamhoofd.svg',
                    'description' => 'Groepsleiding - ' . $functie
                ];
            }
        }
        
        // Search in bestuur
        $bestuur = $stamhoofEntry->get('bestuur', []);
        foreach ($bestuur as $persoon) {
            $naam = $persoon['naam'] ?? '';
            $functie = $persoon['functie'] ?? '';
            
            if (stripos($naam, $query) !== false || stripos($functie, $query) !== false) {
                $results[] = [
                    'title' => $naam,
                    'url' => $stamhoofEntry->url() . '#bestuur',
                    'score' => 75,
                    'type' => 'person',
                    'icon' => '/assets/images/icons/Asset 1stamhoofd.svg',
                    'description' => 'Bestuur - ' . $functie
                ];
            }
        }
        
        // Search in takleiding per tak
        $takkenEntries = Entry::query()
            ->where('collection', 'takken')
            ->where('published', true)
            ->get();
            
        foreach ($takkenEntries as $takEntry) {
            $takleiding = $takEntry->get('takleiding', []);
            foreach ($takleiding as $leider) {
                $naam = $leider['naam'] ?? '';
                $totem = $leider['totem'] ?? '';
                $functie = $leider['functie'] ?? 'Takleiding';
                
                if (stripos($naam, $query) !== false || stripos($totem, $query) !== false) {
                    $results[] = [
                        'title' => $naam . ($totem ? " ($totem)" : ''),
                        'url' => $takEntry->url() . '#takleiding',
                        'score' => 70,
                        'type' => 'person',
                        'icon' => $this->getIconForEntry($takEntry),
                        'description' => $takEntry->get('title', '') . ' - ' . $functie
                    ];
                }
            }
        }
    }
}