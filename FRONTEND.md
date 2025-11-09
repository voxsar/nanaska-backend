# Nanaska AI - Vue.js Frontend Documentation

## Overview

The Nanaska AI frontend is a modern, responsive Vue.js application designed for CIMA exam preparation. It features a beautiful purple/blue theme with glow effects, dark/light mode support, and comprehensive study tools.

## 🚀 Quick Start

### Prerequisites
- Node.js 16+ and npm
- Laravel backend running on port 8000
- Modern web browser

### Installation

```bash
# Install dependencies
npm install

# Build for production
npm run build

# Start Laravel server
php artisan serve
```

Visit `http://localhost:8000` to access the application.

### Development Mode

**Note**: Vite dev server is disabled in CI environments. For local development:

```bash
# Set environment variable to bypass CI check
export LARAVEL_BYPASS_ENV_CHECK=1

# Run dev server
npm run dev
```

## 🎨 Features

### 1. Authentication System
- **Login**: Student authentication with email and password
- **Register**: New student registration
- **Auto-redirect**: Unauthorized users redirected to login
- **Token Management**: JWT tokens stored in localStorage
- **Test Credentials**: 
  - Email: `student@test.com`
  - Password: `password123`

### 2. Dark/Light Mode
- **Toggle Button**: Sun/moon icon in header
- **Persistence**: Theme preference saved in localStorage
- **Smooth Transitions**: Animated theme switching
- **Tailwind Integration**: Uses Tailwind's built-in dark mode

### 3. Dashboard
- **Welcome Message**: Personalized greeting
- **Statistics Cards**: 
  - Completed Questions
  - Mock Exams taken
  - Average Score
  - Study Hours
- **Quick Actions**: Links to all major features
- **Key Features**: Overview of platform capabilities

### 4. Theory Models (10 Models)
1. SWOT Analysis
2. PEST Analysis
3. Porter's Five Forces
4. Ansoff Matrix
5. BCG Matrix
6. Value Chain Analysis
7. McKinsey 7S Framework
8. Balanced Scorecard
9. PESTLE Analysis
10. Stakeholder Analysis

**Usage**: Select a model, provide context, and AI applies it to your case study.

### 5. Practice Questions
- **50 Questions**: Comprehensive practice bank
- **Progress Tracking**: Shows completed vs. remaining
- **Score Display**: Individual and average scores
- **Question Types**: OCS, MCS, SCS levels
- **AI Feedback**: Automated marking and detailed feedback

### 6. Mock Exams
- **List View**: All available mock exams
- **Exam Details**: Duration, questions, status
- **Timed Interface**: Full exam-taking experience
- **Submit Answers**: Integration with backend API
- **Results Tracking**: View past attempts and scores

### 7. Past Papers
- **Archive**: Last 10 post-examiner guidelines
- **Search**: Find papers by topic or keyword
- **Resources**: Question papers, answer guides, marking schemes
- **Examiner Feedback**: Access to past feedback

### 8. AI Q&A System
- **Ask Questions**: About pre-seen materials
- **Real-world Application**: Connect theory to practice
- **Recent Questions**: Quick access to previous queries
- **AI-Powered**: Contextual, intelligent responses

## 📁 Project Structure

```
resources/
├── js/
│   ├── api/
│   │   └── client.js              # Axios API client
│   ├── components/
│   │   └── Layout.vue             # Main layout component
│   ├── stores/
│   │   ├── auth.js                # Authentication store
│   │   └── theme.js               # Theme store
│   ├── views/
│   │   ├── auth/
│   │   │   ├── Login.vue          # Login page
│   │   │   └── Register.vue       # Registration page
│   │   ├── Dashboard.vue          # Main dashboard
│   │   ├── Preseen.vue            # Pre-seen documents
│   │   ├── Questions.vue          # Q&A system
│   │   ├── TheoryModels.vue       # Theory models
│   │   ├── PastPapers.vue         # Past papers
│   │   ├── Practice.vue           # Practice questions
│   │   ├── MockExams.vue          # Mock exams list
│   │   └── MockExamDetail.vue     # Exam interface
│   ├── router/
│   │   └── index.js               # Route definitions
│   ├── App.vue                    # Root component
│   └── app.js                     # Entry point
├── css/
│   └── app.css                    # Tailwind CSS
└── views/
    └── app.blade.php              # Laravel blade template
```

## 🎨 Theming

### Color Palette

**Primary (Purple)**:
- 50: #f5f3ff
- 600: #7c3aed
- 900: #4c1d95

**Secondary (Blue)**:
- 50: #eff6ff
- 600: #2563eb
- 900: #1e3a8a

### Custom Classes

```css
/* Buttons */
.btn-primary       /* Purple/blue gradient button */
.btn-secondary     /* Gray button */

/* Cards */
.card              /* Basic card */
.card-glow         /* Card with hover glow effect */

/* Forms */
.input-field       /* Styled input field */
```

### Glow Effects

```css
.shadow-glow-purple    /* Purple glow */
.shadow-glow-blue      /* Blue glow */
.shadow-glow-purple-lg /* Large purple glow */
.shadow-glow-blue-lg   /* Large blue glow */
```

## 🔌 API Integration

### Endpoints Used

```javascript
// Authentication
POST /api/students/login
POST /api/students/register
POST /api/students/logout

// Past Papers
GET /api/past-papers
GET /api/past-papers/{id}
GET /api/past-papers/{id}/questions

// Mock Exams
GET /api/mock-exams
GET /api/mock-exams/{id}
GET /api/mock-exams/{id}/questions
POST /api/mock-exams/submit-answer
GET /api/mock-exams/attempts/{studentId}

// Submissions
POST /api/students/submit-answer
```

### API Client Configuration

The Axios client (`resources/js/api/client.js`) includes:
- Automatic token injection from localStorage
- Request interceptors for authentication
- Response interceptors for error handling
- Auto-redirect on 401 (unauthorized)

## 🛣 Routing

### Public Routes
- `/login` - Login page
- `/register` - Registration page

### Protected Routes (require authentication)
- `/dashboard` - Main dashboard
- `/preseen` - Pre-seen documents
- `/questions` - Q&A system
- `/theory-models` - Theory models
- `/past-papers` - Past papers archive
- `/practice` - Practice questions
- `/mock-exams` - Mock exams list
- `/mock-exams/:id` - Specific mock exam

### Navigation Guards

```javascript
// Redirect authenticated users away from login/register
router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (to.meta.guest && token) {
    next('/dashboard');
  } else {
    next();
  }
});

// Protect routes requiring authentication
const requireAuth = (to, from, next) => {
  const token = localStorage.getItem('token');
  if (!token) {
    next('/login');
  } else {
    next();
  }
};
```

## 📱 Responsive Design

### Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px

### Responsive Features

- Collapsible navigation menu on mobile
- Stacked cards on small screens
- Adaptive grid layouts
- Touch-friendly buttons and links
- Optimized font sizes

## 🔒 Security

### Implemented Security Measures

1. **JWT Token Management**: Tokens stored securely in localStorage
2. **Auto-logout**: On 401 responses
3. **Protected Routes**: Auth guards prevent unauthorized access
4. **CSRF Protection**: Handled by Laravel backend
5. **Input Validation**: Client-side validation on forms

### Security Considerations

- Tokens are stored in localStorage (consider httpOnly cookies for enhanced security)
- No sensitive data logged in console
- XSS protection via Vue's built-in sanitization

## 🎯 State Management

### Pinia Stores

#### Auth Store (`stores/auth.js`)
```javascript
// State
student       // Current student object
token         // JWT token
isAuthenticated // Boolean

// Actions
login(email, password)
register(name, email, password, passwordConfirmation)
logout()
```

#### Theme Store (`stores/theme.js`)
```javascript
// State
isDark        // Boolean for dark mode

// Actions
toggleTheme() // Switch between light/dark
```

## 🧪 Testing

### Manual Testing Checklist

- [ ] Login with valid credentials
- [ ] Login with invalid credentials (error message)
- [ ] Register new account
- [ ] Logout functionality
- [ ] Dark/light mode toggle
- [ ] Theme persistence (refresh page)
- [ ] Navigate to all pages
- [ ] Mobile responsive layout
- [ ] API integration (past papers, mock exams)
- [ ] Protected routes (try accessing without login)

## 🚀 Production Deployment

### Build for Production

```bash
# Build optimized assets
npm run build

# Assets will be in public/build/
```

### Environment Variables

No additional environment variables needed. The app uses:
- Base URL: `/api` (relative to current domain)
- Auto-detects API endpoint based on current host

### Performance Optimizations

- **Lazy Loading**: Routes loaded on demand
- **Code Splitting**: Separate chunks for each view
- **Tree Shaking**: Unused code removed
- **Minification**: CSS and JS minified
- **Asset Optimization**: Vite optimizes all assets

## 📚 Development Tips

### Adding a New Page

1. Create Vue component in `resources/js/views/`
2. Add route in `resources/js/router/index.js`
3. Add navigation link in `resources/js/components/Layout.vue`
4. Test authentication guard if needed

### Styling Guidelines

- Use Tailwind utility classes
- Follow dark mode: prefix with `dark:`
- Use custom classes from `resources/css/app.css`
- Maintain purple/blue color scheme
- Add glow effects on hover for interactive elements

### API Integration

```javascript
// Import API client
import api from '@/api/client';

// Make requests
const response = await api.get('/endpoint');
const data = response.data;
```

## 🐛 Troubleshooting

### Common Issues

**Issue**: Vite dev server won't start in CI
- **Solution**: Use production build (`npm run build`)

**Issue**: 404 on page refresh
- **Solution**: Laravel handles all routes via `routes/web.php` catch-all

**Issue**: API requests fail
- **Solution**: Ensure Laravel backend is running and CORS is configured

**Issue**: Theme doesn't persist
- **Solution**: Check localStorage permissions in browser

**Issue**: Login doesn't work
- **Solution**: Verify test student exists in database

## 📞 Support

For issues or questions:
1. Check this documentation
2. Review API.md for backend endpoints
3. Check browser console for errors
4. Verify database has test data

## 🎓 Learning Resources

- [Vue 3 Documentation](https://vuejs.org/)
- [Vue Router Documentation](https://router.vuejs.org/)
- [Pinia Documentation](https://pinia.vuejs.org/)
- [Tailwind CSS Documentation](https://tailwindcss.com/)
- [Vite Documentation](https://vitejs.dev/)

---

**Version**: 1.0.0  
**Last Updated**: 2025-11-09  
**Status**: Production Ready ✅
