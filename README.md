# Office Expense Management System

A modern, clean, and user-friendly expense management system built with Laravel, Livewire, and Tailwind CSS.

## Features

### User Roles
- **Admin**: Manage categories, employee wallets, and view comprehensive reports
- **Employee**: Track expenses, manage personal wallet, and view spending analytics

### Wallet System
- Each employee has a dedicated wallet with real-time balance tracking
- Admin can add funds or adjust balances
- Wallets can go negative
- Automatic deduction on expense creation
- Complete transaction history with filtering
- Wallet transaction history page for both admin and employees

### Expense Management
- Add expenses with category, amount, description, and date
- Automatic wallet deduction
- Soft delete support (expenses can be restored)
- Advanced search and filtering:
  - Search by description
  - Filter by category
  - Filter by date range
  - Filter by amount range
- Pagination for large datasets

### Categories
- Admin can create, edit, and delete expense categories
- Categories include name and description
- Used for organizing and analyzing expenses

### Reports & Analytics
- Monthly expense summaries
- Spending by category (with interactive charts)
- Admin dashboard with organization-wide statistics
- Employee dashboard with personal spending analytics
- User detail pages with comprehensive reports
- Export reports as CSV

### Additional Features
- Detailed user profile pages with wallet and expense analytics
- Email-ready notifications (for low balance alerts)
- Responsive design (mobile-friendly)
- Real-time UI updates with Livewire
- Clean, professional Tailwind CSS styling
- Chart.js integration for visual analytics

## Tech Stack

- **Backend**: Laravel 8
- **Frontend**: Livewire 2, Alpine.js, Tailwind CSS
- **Database**: SQLite (configurable to MySQL/PostgreSQL)
- **Charts**: Chart.js
- **Authentication**: Laravel Breeze

## Installation

### Prerequisites
- PHP >= 7.3
- Composer
- Node.js & NPM
- SQLite (or MySQL/PostgreSQL)

### Setup Steps

1. **Install PHP Dependencies**
   ```bash
   composer install
   ```

2. **Install JavaScript Dependencies**
   ```bash
   npm install
   ```

3. **Environment Configuration**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Database Setup**

   The project is configured to use SQLite by default. The database file is already created at `database/database.sqlite`.

   Run migrations:
   ```bash
   php artisan migrate
   ```

5. **Seed Database with Demo Data**
   ```bash
   php artisan db:seed
   ```

   This creates:
   - 1 Admin user (admin@expense.com / password)
   - 3 Employee users:
     - john@expense.com / password
     - jane@expense.com / password
     - bob@expense.com / password
   - 8 expense categories
   - Demo expenses for each employee

6. **Build Assets**
   ```bash
   npm run prod
   ```

   For development with hot reload:
   ```bash
   npm run watch
   ```

7. **Start Development Server**
   ```bash
   php artisan serve
   ```

   Visit http://localhost:8000

## Default Credentials

### Admin Account
- **Email**: admin@expense.com
- **Password**: password

### Employee Accounts
- **John Doe**: john@expense.com / password
- **Jane Smith**: jane@expense.com / password
- **Bob Johnson**: bob@expense.com / password

## Usage Guide

### For Admins

1. **Dashboard**: View all employee wallets, total spending, and analytics
2. **Users**: Manage users and view detailed user profiles
3. **Wallets**: Manage wallet balances and add funds
4. **Wallet History**: View all wallet transactions across users
5. **Reports**: View user expense reports with filtering
6. **Categories**: Manage expense categories (CRUD operations)

### For Employees

1. **Dashboard**: View wallet balance, recent expenses, and spending charts
2. **My Expenses**: View all expenses with advanced filtering and search
3. **Add Expense**: Create new expenses (automatically deducts from wallet)
4. **Wallet History**: View personal wallet transaction history
5. **Export**: Download expense reports as CSV

## Project Structure

```
app/
├── Http/
│   ├── Livewire/
│   │   ├── Admin/
│   │   │   ├── Dashboard.php         # Admin dashboard
│   │   │   ├── Categories.php        # Category management
│   │   │   ├── UserDetail.php        # Detailed user profile
│   │   │   ├── WalletHistory.php     # All wallet transactions
│   │   │   ├── WalletManagement.php  # Wallet operations
│   │   │   └── UserExpenseReport.php # User expense reports
│   │   └── Employee/
│   │       ├── Dashboard.php         # Employee dashboard
│   │       ├── Expenses.php          # Expense list with filters
│   │       ├── AddExpense.php        # Add new expense
│   │       └── TopupHistory.php      # Wallet transaction history
│   └── Middleware/
│       └── AdminMiddleware.php       # Admin role protection
├── Models/
│   ├── User.php                      # User model with roles
│   ├── Wallet.php                    # Wallet with transaction methods
│   ├── Expense.php                   # Expense with auto wallet deduction
│   ├── Category.php                  # Expense categories
│   └── WalletTransaction.php         # Transaction history
database/
├── migrations/                        # Database schema
└── seeders/                          # Demo data seeders
resources/
└── views/
    └── livewire/                     # Livewire component views
routes/
└── web.php                           # Application routes
```

## Key Features Explained

### Automatic Wallet Deduction
When an employee creates an expense, the wallet balance is automatically deducted via model events. This ensures data consistency and prevents manual errors.

### Real-time Updates
Livewire provides real-time UI updates without page refreshes:
- Wallet balances update instantly
- Filters apply in real-time
- Form validation shows immediately

### Soft Deletes
Expenses use soft deletes, meaning:
- Deleted expenses can be restored
- Wallet balances are adjusted on delete/restore
- Complete audit trail is maintained

### Transaction History
Every wallet operation creates a transaction record:
- Funds added by admin
- Expense deductions
- Balance adjustments
- Maintains balance history

### User Detail Pages
Comprehensive user profile pages showing:
- Basic user information
- Current wallet balance
- Wallet transaction statistics
- Expense summary with category breakdown
- Recent wallet transactions

## Extending the System

### Adding Email Notifications

Uncomment the notification code in relevant models and create notification classes:

```bash
php artisan make:notification LowBalanceAlert
php artisan make:notification ExpenseCreated
```

### Switching to MySQL

Update `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=expense_management
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

Then run migrations:

```bash
php artisan migrate:fresh --seed
```

## Security

- CSRF protection enabled on all forms
- Admin routes protected by custom middleware
- SQL injection protection via Eloquent ORM
- XSS protection via Blade templating
- Password hashing with bcrypt

## Performance

- Database queries optimized with eager loading
- Pagination on all list views
- Indexed foreign keys
- Asset minification in production

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Contributing

1. Fork the repository
2. Create a feature branch
3. Commit your changes
4. Push to the branch
5. Create a Pull Request

## License

This project is open-sourced software licensed under the MIT license.

## Support

For issues, questions, or contributions, please contact the development team.

---

**Built with Laravel & Livewire** | **Styled with Tailwind CSS**
