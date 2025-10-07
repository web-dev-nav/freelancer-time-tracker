# Timesheet CSS Modules

This directory contains modular CSS files for the Freelancer Time Tracker timesheet application. The CSS has been extracted from the blade template and organized into separate component files for better maintainability and reusability.

## File Structure

```
public/css/timesheet/
├── main.css                 # Main import file (use this in your HTML)
├── variables.css            # CSS custom properties (colors, spacing, etc.)
├── layout.css              # Main layout and grid systems
├── navigation.css          # Navigation tabs styling
├── project-selector.css    # Project selector dropdown
├── dashboard.css           # Dashboard stats and active session
├── tracker.css             # Time tracker forms and displays
├── history.css             # History table and pagination
├── reports.css             # Reports generation and results
├── projects.css            # Projects grid and cards
├── modals.css              # Modal dialogs and overlays
├── forms.css               # Form controls and buttons
└── utilities.css           # Helper classes
```

## File Details

### 1. **main.css** (45 lines)
Main entry point that imports all component stylesheets. Use this file in your blade template:
```html
<link rel="stylesheet" href="{{ asset('css/timesheet/main.css') }}">
```

### 2. **variables.css** (79 lines)
CSS custom properties documentation and additional variables:
- Color variables (referenced from main layout)
- Spacing scale (xs, sm, md, lg, xl, 2xl, 3xl)
- Transition variables (fast, normal, slow)
- Border radius variables (sm, md, lg, xl, full)

### 3. **layout.css** (58 lines)
Core layout structures:
- Tab content layout
- Dashboard grid system
- Form row layouts
- Responsive grid adjustments

### 4. **navigation.css** (68 lines)
Navigation components:
- Nav tabs container and styling
- Tab buttons (default, hover, active states)
- Tab icons
- Responsive tab wrapping

### 5. **project-selector.css** (75 lines)
Project selection interface:
- Project selector container
- Selector header and label
- Dropdown select styling
- Focus states and hover effects

### 6. **dashboard.css** (176 lines)
Dashboard view components:
- Stats container and stat cards
- Active session card
- Session status indicator with pulse animation
- Quick actions section
- Action buttons

### 7. **tracker.css** (100 lines)
Time tracking interface:
- Tracker container
- Clock section (clock in/out forms)
- Active session display
- Session avatar and visual elements
- Session metadata

### 8. **history.css** (173 lines)
Time log history view:
- History container and header
- History table styling
- Pagination controls and buttons
- Page size selector
- Description preview/truncation

### 9. **reports.css** (168 lines)
Reports generation and display:
- Reports container
- Report generator form
- Quick select buttons
- Report summary cards
- Report results table
- Report period display

### 10. **projects.css** (194 lines)
Projects management view:
- Projects page layout
- Projects grid (responsive auto-fill)
- Project cards with hover effects
- Project stats and badges
- Empty state styling

### 11. **modals.css** (160 lines)
Modal dialog components:
- Base modal styles and positioning
- Modal header, body, and footer
- Modal overlay (backdrop)
- Detail section styling (for view details modal)
- Detail grid and item layouts
- Work description content

### 12. **forms.css** (171 lines)
Form elements and controls:
- Form groups and labels
- Form controls (inputs, textareas, selects)
- Input focus states and placeholders
- Button base styles and variants
  - Primary, secondary, success, danger, warning
  - Outline variants
  - Size variants (sm, lg)
- Button hover and active states

### 13. **utilities.css** (210 lines)
Helper and utility classes:
- Text color utilities (.text-success, .text-danger, etc.)
- Display utilities (.hidden, .visible, etc.)
- Spacing utilities (.m-0, .p-0, etc.)
- Text alignment (.text-left, .text-center, .text-right)
- Flex utilities (.d-flex, .align-items-center, etc.)
- Gap utilities (.gap-1 through .gap-6)
- Width utilities (.w-100, .w-auto)
- Font weight utilities
- Cursor utilities
- Overflow utilities

## Total Statistics

- **Total Files:** 13 CSS files
- **Total Lines:** 1,677 lines of CSS
- **Original Location:** resources/views/timesheet/index.blade.php (lines 629-1565)

## Usage

### Option 1: Use main.css (Recommended)
Include the main CSS file in your blade template:

```html
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timesheet/main.css') }}">
@endpush
```

### Option 2: Import Individual Components
If you only need specific components, import them individually:

```html
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/timesheet/variables.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timesheet/layout.css') }}">
    <link rel="stylesheet" href="{{ asset('css/timesheet/dashboard.css') }}">
    <!-- Add other components as needed -->
@endpush
```

## CSS Variables Used

All CSS files reference variables defined in the main application layout (`resources/views/layouts/app.blade.php`):

**Colors:**
- `--primary-color`, `--primary-dark`
- `--secondary-color`, `--light-color`, `--lighter-color`, `--darker-color`
- `--success-color`, `--warning-color`, `--danger-color`
- `--text-primary`, `--text-secondary`
- `--border-color`

**Shadows:**
- `--shadow`, `--shadow-lg`

## Features

### Modular Organization
Each component is isolated in its own file, making it easy to:
- Find and update specific styles
- Remove unused components
- Override styles in a targeted way
- Understand the scope of each component

### Consistent Formatting
All files follow the same structure:
- Clear section comments with visual separators
- Grouped related styles
- Media queries inline with components
- Consistent spacing and indentation

### Maintainability
- Clear file naming conventions
- Comprehensive comments
- Logical organization by feature
- Easy to extend and customize

## Browser Compatibility

The CSS uses modern features that work in all current browsers:
- CSS Custom Properties (CSS Variables)
- CSS Grid
- Flexbox
- CSS Animations
- Backdrop Filter

## Migration Notes

To migrate from the inline styles in the blade template to these modular files:

1. Remove the `<style>` block from `resources/views/timesheet/index.blade.php` (lines 629-1565)
2. Add the main.css import to the blade template
3. Test all components to ensure styling is preserved
4. Consider moving the CSS variables from the layout file to variables.css if you want full modularity

## Future Enhancements

Potential improvements:
- Add CSS preprocessor (SASS/LESS) for variables and mixins
- Implement CSS purging to remove unused styles in production
- Add dark/light theme variants
- Create a style guide document
- Add CSS linting configuration
