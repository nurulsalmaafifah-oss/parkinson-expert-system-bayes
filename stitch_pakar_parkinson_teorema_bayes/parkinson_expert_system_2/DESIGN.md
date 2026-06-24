---
name: Parkinson Expert System
colors:
  surface: '#f7f9fb'
  surface-dim: '#d8dadc'
  surface-bright: '#f7f9fb'
  surface-container-lowest: '#ffffff'
  surface-container-low: '#f2f4f6'
  surface-container: '#eceef0'
  surface-container-high: '#e6e8ea'
  surface-container-highest: '#e0e3e5'
  on-surface: '#191c1e'
  on-surface-variant: '#434655'
  inverse-surface: '#2d3133'
  inverse-on-surface: '#eff1f3'
  outline: '#737686'
  outline-variant: '#c3c6d7'
  surface-tint: '#0053db'
  primary: '#004ac6'
  on-primary: '#ffffff'
  primary-container: '#2563eb'
  on-primary-container: '#eeefff'
  inverse-primary: '#b4c5ff'
  secondary: '#565e74'
  on-secondary: '#ffffff'
  secondary-container: '#dae2fd'
  on-secondary-container: '#5c647a'
  tertiary: '#006242'
  on-tertiary: '#ffffff'
  tertiary-container: '#007d55'
  on-tertiary-container: '#bdffdb'
  error: '#ba1a1a'
  on-error: '#ffffff'
  error-container: '#ffdad6'
  on-error-container: '#93000a'
  primary-fixed: '#dbe1ff'
  primary-fixed-dim: '#b4c5ff'
  on-primary-fixed: '#00174b'
  on-primary-fixed-variant: '#003ea8'
  secondary-fixed: '#dae2fd'
  secondary-fixed-dim: '#bec6e0'
  on-secondary-fixed: '#131b2e'
  on-secondary-fixed-variant: '#3f465c'
  tertiary-fixed: '#6ffbbe'
  tertiary-fixed-dim: '#4edea3'
  on-tertiary-fixed: '#002113'
  on-tertiary-fixed-variant: '#005236'
  background: '#f7f9fb'
  on-background: '#191c1e'
  surface-variant: '#e0e3e5'
typography:
  headline-lg:
    fontFamily: Inter
    fontSize: 32px
    fontWeight: '700'
    lineHeight: 40px
    letterSpacing: -0.02em
  headline-lg-mobile:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '700'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-md:
    fontFamily: Inter
    fontSize: 24px
    fontWeight: '600'
    lineHeight: 32px
    letterSpacing: -0.01em
  headline-sm:
    fontFamily: Inter
    fontSize: 20px
    fontWeight: '600'
    lineHeight: 28px
  body-lg:
    fontFamily: Inter
    fontSize: 18px
    fontWeight: '400'
    lineHeight: 28px
  body-md:
    fontFamily: Inter
    fontSize: 16px
    fontWeight: '400'
    lineHeight: 24px
  body-sm:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '400'
    lineHeight: 20px
  label-md:
    fontFamily: Inter
    fontSize: 14px
    fontWeight: '600'
    lineHeight: 20px
    letterSpacing: 0.05em
  label-sm:
    fontFamily: Inter
    fontSize: 12px
    fontWeight: '500'
    lineHeight: 16px
rounded:
  sm: 0.25rem
  DEFAULT: 0.5rem
  md: 0.75rem
  lg: 1rem
  xl: 1.5rem
  full: 9999px
spacing:
  base: 8px
  xs: 4px
  sm: 12px
  md: 24px
  lg: 32px
  xl: 48px
  gutter: 24px
  margin-desktop: 40px
  margin-mobile: 16px
---

## Brand & Style
This design system is engineered for high-stakes medical decision support, specifically tailored for the longitudinal tracking and diagnosis of Parkinson’s Disease. The brand personality is **authoritative, precise, and empathetic**. It balances the clinical rigor required by neurologists with a calm, accessible interface that reduces cognitive load during complex data analysis.

The visual style follows a **Modern Corporate** aesthetic with a strong emphasis on **Minimalism**. By prioritizing high-contrast typography and generous whitespace, the system ensures that critical patient metrics—such as tremor frequency or gait analysis—remain the focal point. The interface avoids unnecessary decorative elements, opting instead for functional clarity and a systematic layout that evokes trust and professional reliability.

## Colors
The palette is rooted in a professional "Medical Blue" primary, chosen for its association with healthcare stability and digital clarity. 

- **Primary (#2563EB):** Used for primary actions, active navigation states, and key interactive highlights.
- **Secondary (#0F172A):** A deep navy used for the sidebar and primary headings to provide a strong structural anchor.
- **Success (#10B981):** Specifically reserved for positive clinical outcomes, stable readings, and completed assessments.
- **Background (#F8FAFC):** A cool, light slate that reduces eye strain during long periods of data review.

Semantic colors for **Warning** and **Error** are critical in this design system to alert clinicians to rapid symptom progression or data inconsistencies.

## Typography
The typography utilizes **Inter** exclusively to leverage its exceptional legibility in data-heavy environments. The scale is designed to create a clear information hierarchy, allowing experts to scan patient IDs and clinical scores rapidly.

**Headline-lg** is reserved for page titles and patient names. **Label-md** uses a slightly increased letter spacing and uppercase styling for table headers and section categorizers to differentiate them from interactive data points. For mobile views, the primary headline scales down to maintain screen real estate without losing impact.

## Layout & Spacing
This design system employs a **Fixed Grid** model for the desktop dashboard to ensure that complex data visualizations and charts remain consistent and comparable across different screens. A 12-column grid is used for the main content area, with a fixed-width sidebar (280px) for primary navigation.

The spacing rhythm is based on an **8px base unit**. Margins and gutters are generous to prevent the dense medical data from feeling overwhelming. On mobile devices, the layout reflows into a single column with a 16px side margin, prioritizing the most recent clinical observations and urgent alerts at the top of the stack.

## Elevation & Depth
Depth is communicated through **Tonal Layers** and **Ambient Shadows**. The primary workspace uses the Light Slate background as the base layer. Interactive elements and data containers reside on white "Surface" cards.

Shadows are intentionally subtle (low-opacity navy tint) to maintain a modern, clean look without the heavy aesthetics of traditional skeuomorphism. High-elevation shadows are reserved for temporary overlays like diagnostic modals or dropdown menus. To distinguish secondary information, some containers may use a 1px border in a slightly darker slate rather than a shadow, keeping the interface crisp and professional.

## Shapes
The shape language is defined as **Rounded**, utilizing a 0.5rem (8px) corner radius for standard components like buttons, input fields, and cards. This radius provides a modern, approachable feel that softens the clinical nature of the application while maintaining a structured, professional appearance.

Larger containers, such as dashboard widgets, utilize the `rounded-lg` (16px) specification to create a clear visual distinction between the outer container and the internal elements. Small indicators, such as status pips or notification badges, remain fully circular (pill-shaped).

## Components
- **Buttons:** Primary buttons use a solid Medical Blue fill with white text. Secondary buttons use an outline style with the Deep Navy stroke. All buttons have a height of 40px for comfortable interactivity.
- **Cards:** The central unit of the dashboard. Each card is white with an 8px radius and a subtle 1px border (#E2E8F0). They should include a clear title area and an optional footer for actions.
- **Data Tables:** High-density but legible. Headers are styled with the `label-md` token. Rows use a 48px height with thin horizontal dividers; zebra-striping is avoided to keep the focus on the data.
- **Chips:** Used for patient status (e.g., "Stable," "Observation Required"). These use light tinted backgrounds with high-contrast text in the corresponding semantic color.
- **Sidebars:** Dark-themed using the Deep Navy (#0F172A). Nav items should feature a 4px left-aligned "active" indicator in Medical Blue.
- **Input Fields:** Standardized with a 1px border. The focus state uses a 2px Primary Blue glow to provide clear visual feedback during data entry.
- **Progress Indicators:** Linear bars are preferred for showing symptom progression or assessment completion, utilizing the Success Emerald for positive milestones.