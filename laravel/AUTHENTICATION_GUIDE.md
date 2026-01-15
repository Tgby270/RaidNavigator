# Guide d'Authentification - Routes Protégées

## 📋 Vue d'ensemble

Ce document décrit comment les routes sont protégées par authentification dans l'application.

## 🔒 Middleware `auth`

Le middleware `auth` de Laravel vérifie si l'utilisateur est connecté. Si ce n'est pas le cas, il redirige automatiquement vers la page de connexion (`/login`).

## 🗂️ Organisation des Routes

### Routes Publiques (Accessibles sans connexion)

```php
// Page d'accueil
Route::get('/', ...)

// Visualisation des raids et courses
Route::get('/raid/{raid_id}', ...)
Route::get('/course-detail/{course_id}/{raid_id}', ...)

// Authentification
Route::get('/login', ...)
Route::post('/login', ...)
Route::get('/register', ...)
Route::post('/register', ...)

// Tests
Route::get('/test', ...)
```

### Routes Protégées (Nécessitent une connexion)

Toutes les routes ci-dessous sont dans un groupe `Route::middleware(['auth'])->group(function () { ... })` :

#### 🏆 Gestion des RAIDS
- `GET /CreateRaid` - Afficher le formulaire de création de raid
- `POST /raid/create` - Créer un nouveau raid

#### 🏃 Gestion des COURSES
- `GET /raids/{raid}/courses/create` - Créer une course pour un raid
- `POST /courses/{raid}/create` - Soumettre une nouvelle course
- `GET CreateCourse` - Ancienne route (compatibilité)

#### 🏢 Gestion des CLUBS
- `GET /CreateClub` - Afficher le formulaire de création de club
- `POST /club/create` - Créer un nouveau club

#### 💰 Gestion des TARIFS
- `GET /raids/{raid}/courses/{crs}/tarifs` - Gérer les tarifs
- `POST /raids/{raid}/courses/{crs}/tarifs` - Sauvegarder les tarifs

#### 👥 Gestion des ÉQUIPES
- `GET /equipe/create` - Créer une équipe
- `GET /equipe/modify/{raid_id}/{course_id}/{equ_id?}` - Modifier une équipe
- `POST /equipe/store` - Enregistrer une équipe
- `PUT /equipe/update/{equ}` - Mettre à jour une équipe
- `POST /equipe/add/{id}` - Ajouter un membre
- `POST /equipe/deleteMember/{id}` - Supprimer un membre
- `DELETE /equipe/{raid}/{crs}/{equ}` - Supprimer une équipe

#### 📧 Invitations
- `GET /invitation/accept` - Accepter une invitation
- `GET /invitation/decline` - Refuser une invitation

## 🔧 Comment ça fonctionne

### 1. Utilisateur connecté
```
Utilisateur connecté → Accès à /CreateRaid → ✅ Formulaire affiché
```

### 2. Utilisateur non connecté
```
Utilisateur déconnecté → Accès à /CreateRaid → 🔄 Redirection vers /login
```

### 3. Après connexion
```
Login réussi → Redirection vers la page demandée initialement
```

## 💡 Utilisation dans le code

### Ajouter une nouvelle route protégée

```php
// Dans routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::get('/ma-route-protegee', function () {
        // Code accessible uniquement si connecté
    });
});
```

### Vérifier si l'utilisateur est connecté dans une vue

```php
// Dans un contrôleur ou une closure de route
use Illuminate\Support\Facades\Auth;

if (Auth::check()) {
    // L'utilisateur est connecté
    $user = Auth::user();
}
```

### Dans une vue Blade (si utilisé)

```blade
@auth
    <!-- Contenu visible uniquement si connecté -->
    <p>Bonjour {{ Auth::user()->name }}</p>
@endauth

@guest
    <!-- Contenu visible uniquement si déconnecté -->
    <a href="/login">Se connecter</a>
@endguest
```

### Dans React/Inertia

```jsx
import { usePage } from '@inertiajs/react';

export default function MonComposant() {
    const { auth } = usePage().props;
    const user = auth?.user;
    
    return (
        <div>
            {user ? (
                <p>Bonjour {user.name}</p>
            ) : (
                <a href="/login">Se connecter</a>
            )}
        </div>
    );
}
```

## 🎯 Configuration du middleware

Le middleware `auth` est défini dans `app/Http/Kernel.php` et redirige vers la route définie dans `app/Http/Middleware/Authenticate.php` :

```php
protected function redirectTo($request)
{
    if (! $request->expectsJson()) {
        return route('login');
    }
}
```

## 🔐 Sécurité

### Bonnes pratiques

1. ✅ **Toujours protéger les routes de création/modification** avec le middleware `auth`
2. ✅ **Valider les permissions** en plus de l'authentification (ex: seul le manager peut modifier son équipe)
3. ✅ **Ne jamais faire confiance aux données du client** - toujours valider côté serveur
4. ✅ **Utiliser les tokens CSRF** (automatique avec Laravel)

### Exemple de validation supplémentaire

```php
Route::middleware(['auth'])->group(function () {
    Route::get('/equipe/modify/{raid}/{course}/{equ}', function ($raid, $course, $equ) {
        $user = Auth::user();
        $equipe = EQUIPE::findOrFail($equ);
        
        // Vérifier que l'utilisateur est le manager de l'équipe
        if ($equipe->USE_ID !== $user->USE_ID) {
            abort(403, 'Vous n\'êtes pas autorisé à modifier cette équipe');
        }
        
        // Suite du code...
    });
});
```

## 📚 Ressources

- [Documentation Laravel - Authentication](https://laravel.com/docs/11.x/authentication)
- [Documentation Laravel - Middleware](https://laravel.com/docs/11.x/middleware)
- [Documentation Inertia - Shared Data](https://inertiajs.com/shared-data)

## 🧪 Tester l'authentification

### Manuellement
1. Déconnectez-vous : `/logout`
2. Essayez d'accéder à `/CreateRaid`
3. Vous devriez être redirigé vers `/login`
4. Connectez-vous
5. Vous devriez être redirigé vers `/CreateRaid`

### Avec des tests automatisés
```php
// tests/Feature/AuthenticationTest.php
public function test_protected_routes_redirect_to_login()
{
    $response = $this->get('/CreateRaid');
    $response->assertRedirect('/login');
}

public function test_authenticated_user_can_access_protected_routes()
{
    $user = User::factory()->create();
    
    $response = $this->actingAs($user)->get('/CreateRaid');
    $response->assertStatus(200);
}
```

## ⚠️ Déconnexion

Pour se déconnecter :
```php
Route::get('/logout', function () {
    Auth::logout();
    return redirect('/login');
})->name('logout');
```

Dans React/Inertia :
```jsx
<Link href="/logout">Se déconnecter</Link>
```
