# Lifestyle Smiles – custom WordPress theme

## 1. Overview
A builder-free custom theme for the Lifestyle Smiles same-day smile landing page. It is built from the Figma frame "desktop - smile" (section screenshots) using PHP, semantic HTML5, modern CSS and vanilla JavaScript. The landing page renders from `front-page.php`.

## 2. Theme structure
```
smile-custom-theme/
├── style.css            Theme header only
├── functions.php        Setup, enqueues, resource hints, meta description
├── index.php            Fallback template
├── front-page.php       Landing page (assembles template parts)
├── header.php / footer.php
├── inc/
│   ├── helpers.php      Options, icons, nav, CTA button
│   ├── customizer.php   Practice details (Appearance > Customize > Practice details)
│   └── form-handler.php Suitability form handler (admin-post.php, nonce, validation, wp_mail)
├── template-parts/      hero, refresh, stories, why, dentist, suitability
├── assets/
│   ├── css/main.css
│   ├── js/main.js       Mobile nav + 2-step form
│   ├── images/          See "Known limitations"
│   └── fonts/           Poppins (woff)
└── screenshot.png
```

## 3. Installation
1. Zip the `smile-custom-theme` folder (the folder itself must be the zip root).
2. WordPress admin > Appearance > Themes > Add New > Upload Theme, then Activate.
3. Settings > Reading: set "Your homepage displays" to *Your latest posts* or any page (`front-page.php` is used either way).
4. Appearance > Customize > **Practice details**: set phone, address, booking URL, notification email, privacy/disclaimer URLs and (optionally) the hero image.
5. Appearance > Menus: create a menu and assign it to **Primary menu** (until then, placeholder anchor links are shown).

## 4. Requirements
WordPress 6.0+, PHP 7.4+. No plugins required. Outgoing email must work (use an SMTP plugin such as WP Mail SMTP in production).

## 5. Local development
Use Local (LocalWP), Docker (`wordpress:latest`) or XAMPP. Place the theme in `wp-content/themes/`, enable `WP_DEBUG` and `WP_DEBUG_LOG` in `wp-config.php`, and use a mail catcher (e.g. MailHog / Local's Mailhog) to inspect form emails.

## 6. Build / deployment
No build step. Deploy by uploading the theme folder (or zip) to `wp-content/themes/` and activating it. Serve over HTTPS (the form copy says data is "transmitted securely").

## 7. Known limitations
- **Images are low-resolution crops from the supplied screenshots**, not the original Figma exports. Replace `assets/images/{couple,patient-story,dr-julie}.jpg` and `logo.png` (ideally SVG) with the real assets.
- **Hero media**: the Figma frame shows an empty (checkerboard) placeholder. The hero shows an image only if one is set in the Customizer.
- **Playfair Display** is loaded from Google Fonts. To self-host, add the woff2 files to `assets/fonts/`, add `@font-face` rules and remove the `smile-fonts` enqueue.
- Phone number, notification email, nav destinations, Disclaimer/Privacy URLs and the CTA URL are placeholders until set.
- Patient stories show the same story twice (as in the Figma frame). The dentist carousel arrows/dots are omitted because only one photo exists.
- The form emails the photos as attachments (5 MB max each); photos are not stored on the server. For production volumes consider a secure storage workflow.
- Only desktop and mobile layouts were available; tablet is interpolated.
- Date of birth uses the native date picker rather than a typed DD/MM/YYYY mask.

## 8. Testing checklist
- [ ] Activate theme; no PHP notices with `WP_DEBUG` on
- [ ] Page renders at 375, 430, 768, 1280, 1440 px with no horizontal scroll
- [ ] Mobile menu opens/closes (button and Esc), links scroll to sections
- [ ] Phone link opens dialer; Get Directions opens Maps
- [ ] Form: required-field, email, phone, photo type/size validation (client and server)
- [ ] Form: successful submission email arrives with 4 attachments; failure shows an error
- [ ] Keyboard-only navigation, visible focus, skip link
- [ ] Lighthouse accessibility/SEO/performance review on the live site
