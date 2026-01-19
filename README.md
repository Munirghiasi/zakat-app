# Zakat Calculator & Distribution Tracker

A full-stack web application for Muslims to manage their wealth, calculate Zakat according to Hanafi Fiqh, and distribute it in a Sharia-compliant, auditable way.

## Features

- ✅ **User Authentication** - Laravel Breeze with registration, login, and password reset
- ✅ **Wealth Management** - Track zakatable and non-zakatable assets
- ✅ **Debt Management** - Record debts to calculate net zakatable wealth
- ✅ **Zakat Calculation** - Automatic calculation based on Hanafi Fiqh (2.5% of net wealth above Nisab)
- ✅ **Distribution Tracking** - Record Zakat distributions to valid recipients
- ✅ **Sharia Compliance** - Only allows distribution to 8 valid categories (Qur'an 9:60)
- ✅ **Audit Logging** - Complete audit trail of all financial transactions
- ✅ **Admin Panel** - Manage Nisab settings, view statistics, manage users
- ✅ **History** - View past Zakat years and distributions

## Technology Stack

- **Backend**: PHP 8.2+, Laravel 12
- **Frontend**: HTML, CSS, Bootstrap 5, JavaScript
- **Database**: MySQL (via Docker)
- **Authentication**: Laravel Breeze

## Installation

### Prerequisites

- PHP 8.2 or higher
- Composer
- Node.js and npm
- Docker and Docker Compose

### Setup Steps

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd Zakat
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Update .env file**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=zakat_db
   DB_USERNAME=zakat
   DB_PASSWORD=secret
   ```

6. **Start Docker MySQL**
   ```bash
   docker-compose up -d
   ```

7. **Run migrations**
   ```bash
   php artisan migrate
   ```

8. **Seed database**
   ```bash
   php artisan db:seed
   ```

9. **Create storage link**
   ```bash
   php artisan storage:link
   ```

10. **Build assets**
    ```bash
    npm run build
    ```

11. **Start development server**
    ```bash
    php artisan serve
    ```

12. **Access the application**
    - URL: http://localhost:8000
    - Admin Login: admin@zakat.com (password set during seeding)
    - Regular User: test@example.com (password set during seeding)

## Default Admin Credentials

After seeding, you can login with:
- **Email**: admin@zakat.com
- **Password**: (check UserFactory or set manually)

To create an admin user manually:
```bash
php artisan tinker
```
```php
User::create([
    'name' => 'Admin',
    'email' => 'admin@zakat.com',
    'password' => Hash::make('password'),
    'is_admin' => true,
]);
```

## Usage

### For Users

1. **Register/Login** - Create an account or login
2. **Add Assets** - Record your zakatable and non-zakatable assets
3. **Add Debts** - Record your debts (loans, credit cards, bills, etc.)
4. **View Zakat Summary** - See your calculated Zakat obligation
5. **Add Recipients** - Add Zakat recipients (must be from valid Sharia categories)
6. **Record Distributions** - Record when you distribute Zakat
7. **View History** - Review past years' Zakat records

### For Admins

1. **Access Admin Panel** - Navigate to Admin section
2. **Manage Nisab Settings** - Update gold price and Nisab value
3. **View Statistics** - See system-wide statistics
4. **Manage Users** - View and manage user accounts

## Zakat Calculation (Hanafi Fiqh)

The system calculates Zakat according to Hanafi school:

1. **Nisab**: Gold price per gram × 87.48 grams
2. **Total Assets**: Sum of all zakatable assets (cash, bank, gold, silver, business inventory, money owed, crypto, investments)
3. **Total Debts**: Sum of all debts (loans, credit cards, rent, bills, salary owed)
4. **Net Zakatable Wealth**: Total Assets - Total Debts
5. **Zakat Due**: If Net Wealth ≥ Nisab, then Zakat = Net Wealth × 0.025 (2.5%)

## Valid Zakat Recipients (Qur'an 9:60)

1. Fuqara (Poor)
2. Masakin (Needy)
3. Zakat Workers
4. New Muslims
5. Slaves
6. Debtors
7. Fi Sabilillah (In the cause of Allah)
8. Travelers

**Forbidden Recipients**: Parents, Children, Spouse, Wealthy people

## Database Structure

- `users` - User accounts
- `assets` - User assets (zakatable and non-zakatable)
- `debts` - User debts
- `zakat_years` - Zakat calculation years
- `nisab_settings` - Nisab configuration
- `zakat_calculations` - Calculated Zakat amounts
- `recipients` - Zakat recipients
- `distributions` - Zakat distributions
- `audit_logs` - Audit trail

## Security Features

- ✅ User authentication and authorization
- ✅ Data isolation (users can only see their own data)
- ✅ Admin-only access to admin panel
- ✅ Input validation
- ✅ SQL injection protection (Eloquent ORM)
- ✅ XSS protection (Blade templating)
- ✅ CSRF protection
- ✅ Audit logging for all changes

## Development

### Running Tests
```bash
php artisan test
```

### Code Style
```bash
./vendor/bin/pint
```

### Database Reset
```bash
php artisan migrate:fresh --seed
```

## Docker Commands

```bash
# Start MySQL
docker-compose up -d

# Stop MySQL
docker-compose down

# View logs
docker-compose logs mysql

# Reset database volume
docker-compose down -v
```

## Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Submit a pull request

## License

This project is open-source software licensed under the MIT license.

## Support

For issues and questions, please open an issue on the repository.

## Acknowledgments

- Built with Laravel Framework
- Uses Bootstrap for UI
- Follows Hanafi Fiqh for Zakat calculations
- Based on Qur'an 9:60 for valid Zakat recipients
