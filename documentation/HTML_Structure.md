# HTML Structure Documentation for NexusBankSystem

## Overview of HTML Organization

The HTML structure of the NexusBankSystem website is organized in a clear, semantic, and accessible manner across multiple pages including the homepage, About Us, Contact, and Services pages. Each page begins with the standard `<!DOCTYPE html>` declaration and includes the essential `<html>`, `<head>`, and `<body>` tags. The content within the `<body>` is divided into meaningful sections using semantic HTML5 elements to enhance readability, maintainability, and accessibility.

The main layout is consistently structured as follows on these pages:

- **Header (`<header>`)**: Contains the site branding and primary navigation.
- **Navigation (`<nav>`)**: Located inside the header, it provides links to key pages and authentication actions.
- **Page Title or Hero Section (`<section class="page-title">` or `<section class="hero">`)**: Serves as the prominent introductory area with a headline, description, and call-to-action button.
- **Content Sections (`<section>`)**: The main content is divided into thematic sections such as features, about content, contact form, or services grid.
- **Footer (`<footer>`)**: Contains contact information, service links, policy links, and copyright.

This organization ensures a logical flow from branding and navigation, through key content, to footer information, providing a consistent user experience.

## Use of Semantic Tags

Semantic HTML tags are used thoughtfully and consistently across pages to convey the meaning and structure of the content:

- **`<header>`**: Encapsulates the top section of the page, including the logo and navigation menu. It is fixed at the top for persistent access.
- **`<nav>`**: Nested within the header, this element groups the primary navigation links (`Home`, `About Us`, `Services`, `Contact`) and authentication buttons (`Login`, `Sign Up`). This clearly indicates to browsers and assistive technologies that these are navigation controls.
- **`<section>`**: Used to divide the main content into thematic areas:
  - `.hero` or `.page-title` sections introduce the site or page with a large heading and call-to-action or descriptive text.
  - Other sections such as `.features`, `.about-content`, `.contact-section`, and `.services` present specific content relevant to each page.
- **`<footer>`**: Contains site-wide footer information such as contact details, service categories, policy links, and copyright.

While the `<main>` tag is not explicitly used, the main content is effectively segmented using `<section>` elements with descriptive class names, which serve a similar purpose in defining the primary content areas.

## Accessibility Features

Accessibility considerations are integrated into the HTML structure across all pages to improve usability for all users, including those using assistive technologies:

- **Alt Text**: The logo image (`<img src="assets/images/Logo-color-1.png" alt="Nexus Bank Logo" />`) includes descriptive alt text, ensuring screen readers can convey the branding.
- **ARIA Attributes**: The hamburger menu button includes `aria-label="Toggle menu"`, `role="button"`, and `tabindex="0"` attributes. These provide semantic meaning and keyboard accessibility, allowing users to toggle the navigation menu via keyboard or screen readers.
- **Heading Hierarchy**: The pages use a clear and logical heading structure:
  - `<h1>` for the main page title or hero title (e.g., "Where Money Meets Trust", "About Us", "Contact Us", "Our Services").
  - `<h2>` for section titles such as "Our Banking Services", "Get in Touch", or "Our Story".
  - `<h3>` for individual feature cards, service cards, and footer column headings.
  
  This hierarchy helps screen reader users understand the page structure and navigate content efficiently.
- **Form Accessibility**: The Contact page includes a contact form with properly associated `<label>` elements for each input and textarea, enhancing form accessibility.
- **Link Text**: Navigation and footer links use meaningful text labels, aiding comprehension and navigation.
- **Keyboard Accessibility**: The hamburger menu is focusable and operable via keyboard, enhancing navigation on smaller screens.

## User Section HTML Structure and Purpose

The user section of the NexusBankSystem website, exemplified by pages such as the user dashboard, employs a more complex and application-like HTML structure to support interactive and data-driven features.

- **`<aside>`**: Used for the sidebar navigation containing user profile information, navigation links to various user functionalities (e.g., deposit, withdraw, transfer, loans, profile), and logout action. This semantic tag clearly separates the navigation from the main content.
- **`<nav>`**: Nested within the aside, it groups the user-specific navigation links, enhancing accessibility and clarity.
- **`<main>`**: Contains the primary content area of the page, including:
  - A `<header>` with the page title and a hamburger button for responsive navigation.
  - Multiple content sections with headings (`<h1>`, `<h2>`) for account summary, quick actions, recent transactions, and charts.
- **Accessibility Features**:
  - Images such as profile pictures and icons include descriptive alt text.
  - Buttons and interactive elements are clearly labeled.
  - The semantic separation of navigation and main content aids screen reader users.

This structure supports a dashboard-style interface, providing users with a clear, navigable, and accessible experience.

## Admin Section HTML Structure and Purpose

The admin section, as seen in the admin dashboard, also uses a structured semantic HTML layout tailored for administrative tasks and data presentation.

- **`<aside>`**: Serves as the sidebar containing the admin logo, navigation links to various management pages (users, loans, investments, roles, transactions, messages), and logout functionality.
- **`<nav>`**: Inside the aside, it groups the admin navigation links, providing clear and accessible navigation.
- **`<main>`**: The main content area includes:
  - A `<header>` with the page title and a hamburger menu button.
  - Content sections with headings (`<h1>`, `<h2>`, `<h3>`) for system overview statistics and recent user tables.
- **Accessibility Features**:
  - Use of alt text on images.
  - Proper table markup with `<thead>`, `<tbody>`, and `<th>` elements for data tables.
  - Clear heading hierarchy for content organization.

This semantic structure facilitates efficient administration by clearly separating navigation and content, enhancing usability and accessibility.

## Summary

The NexusBankSystem HTML structure demonstrates good practices in semantic markup and accessibility consistently across public, user, and admin sections. The use of semantic tags like `<header>`, `<nav>`, `<section>`, `<footer>`, `<aside>`, and `<main>` clearly defines the page layout and content areas. Accessibility features such as alt text, ARIA attributes, proper form labeling, and a logical heading hierarchy ensure the site is usable by a wide range of users, including those relying on assistive technologies.

This consistent and thoughtful structure supports maintainability, SEO, and an inclusive user experience throughout the entire website.
