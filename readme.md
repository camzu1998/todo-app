# To-Do App - Laravel + Vue.js

Prosta aplikacja do zarządzania zadaniami z backendem w Laravel i frontendem w Vue.js.

## 🚀 Technologie

### Backend
- **Laravel 12**
- **PHP 8.4**
- **MySQL 8.0**

### Frontend
- **Vue.js 3**
- **Vite**
- **Tailwind CSS v4**
- **Axios**
- **SweetAlert2**
- **FontAwesome**

## 📋 Funkcjonalności

- ✅ Dodawanie nowych zadań
- ✅ Edycja istniejących zadań
- ✅ Oznaczanie zadań jako ukończone/w trakcie
- ✅ Usuwanie zadań z potwierdzeniem
- ✅ Walidacja formularzy
- ✅ Responsywny interfejs
- ✅ Powiadomienia o sukcesach/błędach

## 🐳 Uruchomienie z Docker

### Wymagania
- Docker
- Docker Compose

### Instalacja

1. **Sklonuj repozytorium:**
```bash
git clone https://github.com/camzu1998/todo-app.git
cd todo-app
```

2. **Uruchom aplikację:**
```bash
docker-compose up -d
```

3. **Konfiguracja backend**
```bash
cp backend/.env.example backend/.env
docker-compose exec backend php artisan key:generate
docker-compose exec backend php artisan migrate
docker-compose exec backend php artisan storage:link
```
4. **Konfiguracja frontend**
```bash
cp frontend/.env.example frontend/.env
```

### Dostęp do aplikacji

- **Frontend:** http://localhost:5173/
- **Backend API:** http://localhost:8000/api
- **Laravel:** http://localhost:8000


## 🔗 API Endpoints

| Metoda | Endpoint | Opis |
|--------|----------|------|
| GET | `/api/tasks` | Pobierz wszystkie zadania |
| POST | `/api/tasks` | Utwórz nowe zadanie |
| PUT | `/api/tasks/{id}` | Zaktualizuj zadanie |
| DELETE | `/api/tasks/{id}` | Usuń zadanie |

## 🧪 Testy

```bash
# Backend
docker-compose exec backend php artisan test
```

## 📄 Licencja

Ten projekt jest licencjonowany na warunkach MIT.