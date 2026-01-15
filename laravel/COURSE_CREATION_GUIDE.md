# Guide d'implémentation : Création de Course avec RaidId

## Vue d'ensemble

Ce guide explique comment une course est créée et associée à un raid spécifique.

## Architecture

### 1. Routes (web.php)

```php
// Route pour créer une course depuis un raid spécifique
Route::get('/raids/{raid}/courses/create', [CourseController::class, 'create'])
    ->name('courses.create');

// Route pour soumettre le formulaire de création
Route::post('/courses/{raid}/create', [CourseController::class, 'store'])
    ->name('courses.store');
```

### 2. Contrôleur (CourseController.php)

#### Méthode `create()`
- Accepte `$raidId` comme paramètre
- Récupère les informations du raid si fourni
- Passe le `raid_id` et les données du `raid` à la vue React

#### Méthode `store()`
- Accepte `$raidId` comme paramètre de route
- Valide les données du formulaire
- Vérifie que le raid existe
- Crée la course avec `RAID_ID` = `$raidId`
- Redirige vers la page de détail du raid

### 3. Composant React (CreateCourse.jsx)

#### Props reçues
- `users`: Liste des utilisateurs
- `raid_id`: ID du raid (peut être null)
- `raid`: Objet raid complet (peut être null)

#### Fonctionnement
```jsx
const submit = (e) => {
    e.preventDefault();
    // Construction de l'URL avec raid_id
    const submitUrl = raid_id ? `/courses/${raid_id}/create` : '/courses/create';
    post(submitUrl, dataToSend);
};
```

### 4. Page RaidDetail

#### Bouton de création
```jsx
<Link 
    href={`/raids/${raid_id}/courses/create`}
    className="px-6 py-3 bg-green-600..."
>
    Créer une course
</Link>
```

## Flux d'utilisation

1. **L'utilisateur accède à la page d'un raid** 
   - URL: `/raid/{raid_id}`
   - Affiche les détails du raid et ses courses

2. **L'utilisateur clique sur "Créer une course"**
   - Redirige vers: `/raids/{raid_id}/courses/create`
   - Le composant CreateCourse reçoit le `raid_id`

3. **L'utilisateur remplit le formulaire**
   - Les champs du formulaire sont remplis
   - Le raid_id est conservé en mémoire

4. **L'utilisateur soumet le formulaire**
   - POST vers: `/courses/{raid_id}/create`
   - Le contrôleur crée la course avec `RAID_ID` = `raid_id`

5. **Redirection après création**
   - Retour vers: `/raid/{raid_id}`
   - La nouvelle course apparaît dans la liste

## Points importants

### Association automatique
- Le `raid_id` est passé dans l'URL, pas dans le formulaire
- Cela évite toute manipulation côté client
- Plus sécurisé et plus simple

### Validation
```php
// Dans CourseController::store()
$raid = \App\Models\RAIDS::find($raidId);
if (!$raid) {
    return redirect()->back()->withErrors(['error' => 'Le RAID n\'existe pas.']);
}
```

### Génération du CRS_ID
```php
$lastCourse = COURSE::where('RAID_ID', $raidId)
    ->orderBy('CRS_ID', 'desc')
    ->first();
$crsId = $lastCourse ? $lastCourse->CRS_ID + 1 : 1;
```

## Exemples d'utilisation

### Depuis la page d'un raid
```
/raid/1 → Bouton "Créer une course" → /raids/1/courses/create
```

### Depuis un lien direct
```jsx
<Link href={`/raids/${raid.RAID_ID}/courses/create`}>
    Ajouter une course à ce raid
</Link>
```

### Avec compatibilité ancienne route
Si vous avez besoin de créer une course sans raid spécifique:
```
/CreateCourse (raid_id sera null)
```

## Dépannage

### La course n'est pas associée au raid
- Vérifiez que `$raidId` est bien passé dans l'URL de soumission
- Vérifiez le log: `Log::info('Creating course', ['raid_id' => $raidId])`

### Erreur "Le RAID n'existe pas"
- Vérifiez que le `raid_id` existe dans la base de données
- Vérifiez la route: `/courses/{raid}/create` (raid doit être un ID valide)

### La redirection ne fonctionne pas
- Vérifiez: `return Inertia::location('/raid/' . $raidId);`
- Assurez-vous que la route `/raid/{raid_id}` existe

## Structure de la base de données

```
COURSE
├── RAID_ID (FK vers RAIDS)
├── CRS_ID (ID unique par raid)
├── USE_ID (Responsable)
└── ... autres champs
```

La clé primaire est composite: `(RAID_ID, CRS_ID)`
