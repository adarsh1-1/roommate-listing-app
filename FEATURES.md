# RoomMate App - Feature Overview

## 🎯 Landing Page Features

```
┌─────────────────────────────────────────────────┐
│  [RoomMate Logo]    [Browse] [Add Listing]      │ ← Navigation Bar
│                     [Login] [Sign Up]           │
└─────────────────────────────────────────────────┘

┌─────────────────────────────────────────────────┐
│                    🌈 HERO SECTION              │
│  Find Your Perfect Living Space                 │
│  [Search by location] [All Types ▼] [Search]   │ ← Search Bar
└─────────────────────────────────────────────────┘

Featured Listings
┌──────────┐  ┌──────────┐  ┌──────────┐
│ [Image]  │  │ [Image]  │  │ [Image]  │
│ 2BHK     │  │ 1BHK     │  │ Studio   │
│ Bandra   │  │ Andheri  │  │ Colaba   │
│ ₹45k     │  │ ₹32k     │  │ ₹28k     │
│ ⭐4.5    │  │ ⭐4.8    │  │ ⭐4.3    │
│[Details] │  │[Details] │  │[Details] │
└──────────┘  └──────────┘  └──────────┘
```

## 📖 Listing Details Page

```
┌────────────────────────────────────────────────┐
│         [Main Image Gallery]        [❤️Save]   │
│    ┌──┐ ┌──┐ ┌──┐ ┌──┐                       │
│    │  │ │  │ │  │ │  │  ← Thumbnail Gallery │
│    └──┘ └──┘ └──┘ └──┘                       │
└────────────────────────────────────────────────┘

Title: 2BHK Apartment in Bandra
Location: Bandra East ⭐ 4.5 (12 reviews)

Cost Breakdown:
[Rent: ₹45k] [Deposit: ₹90k] [Available: Now]

Details Grid:
[Room: 2BHK] [Area: 850 sqft] [Furnish: Semi]

About This Property
Lorem ipsum dolor sit amet...

Amenities:
[✓ WiFi] [✓ AC] [✓ Washing] [✓ Parking]
[✓ Security] [✓ Gym] [✓ Hall] [✓ Backup]

                    Right Sidebar:
                    ┌──────────────┐
                    │  OWNER INFO  │
                    │  👤 Rajesh   │
                    │  [📞 Call]   │
                    │  [💬 WhatsApp]│
                    │  [✉️ Email]   │
                    │  [❤️ Save]    │
                    │  ⓘ Safety Tip│
                    └──────────────┘
```

## 🔐 Login Page

```
┌────────────────────────────┐
│  Welcome Back              │
│  Sign in to RoomMate       │
│                            │
│  Email: [____________]     │
│  Password: [________]      │
│  ☐ Remember me             │
│  [Forgot Password?]        │
│                            │
│  [Sign In Button]          │
│                            │
│  ─── Or continue with ─── │
│  [Google] [Facebook]       │
│                            │
│  Don't have account?       │
│  [Sign Up]                 │
└────────────────────────────┘
```

## ✍️ Sign Up Page

```
┌────────────────────────────┐
│  Create Account            │
│  Join RoomMate             │
│                            │
│  Full Name: [____________] │
│  Email: [______________]   │
│  Phone: [______________]   │
│  Password: [__________]    │
│  Confirm: [__________]     │
│  ☐ I agree to Terms       │
│                            │
│  [Create Account]          │
│                            │
│  Already have account?     │
│  [Sign In]                 │
└────────────────────────────┘
```

## ➕ Add Listing Page

```
Post a New Listing

[Upload Section]
┌─────────────────────────────────┐
│  ☁️ Drag & Drop Image Here     │
│  or [Select Image Button]       │
│  JPG, PNG, GIF up to 5MB        │
│  [Image Preview Area]           │
└─────────────────────────────────┘

[Form Fields]
Property Title: [_________________]
Location: [_________________]

Rent (₹): [______] Type: [Select ▼] Area: [___]

Furnishing:
○ Unfurnished  ○ Semi-Furnished  ○ Furnished

Description: [_________________]
             [_________________]
             [_________________]

[Publish] [Cancel]
```

## 🎨 Design Elements

### Color Palette
- **Primary Gradient**: Purple (#667eea) → Pink (#764ba2)
- **Accent**: Pink (#ec4899)
- **Background**: Light Gray (#f9fafb)
- **Text Primary**: Dark Gray (#111827)
- **Text Secondary**: Medium Gray (#6b7280)

### Typography
- **Headlines**: Bold, 2.5-4.5rem
- **Subheadings**: Semibold, 1.25-2rem
- **Body**: Regular, 0.875-1rem
- **Labels**: Semibold, 0.875rem

### Spacing
- **Card Padding**: 20px (5 = 1.25rem)
- **Section Padding**: 48-80px (12-20)
- **Gap Between Cards**: 24px (6)
- **Border Radius**: 24px on cards, 12px on inputs

### Shadows
- **Card Hover**: 0 20px 25px -5px rgba(0,0,0,0.1)
- **Subtle**: 0 1px 3px rgba(0,0,0,0.1)
- **Default**: box-shadow on focus

## 📱 Responsive Breakpoints

```
Mobile (<640px)
├─ Single column grid
├─ Full-width cards
├─ Stacked forms
└─ Mobile-optimized spacing

Tablet (640-1024px)
├─ 2-column grid
├─ Medium spacing
└─ Readable typography

Desktop (>1024px)
├─ 3-column grid
├─ Sidebar layouts
├─ Full-featured views
└─ Optimized spacing
```

## 🔄 User Flow

```
Start
  ↓
Landing Page
  ├─ Search → Results filtered
  ├─ View Details → Full page with gallery
  │   ├─ Call/WhatsApp/Email → Contact
  │   └─ Save → Saved listings
  │
  ├─ Add Listing → Upload form
  │   ├─ Choose image → Preview
  │   ├─ Fill details → Validation
  │   └─ Publish → Success
  │
  └─ Login/Signup
      └─ Authentication → User Dashboard

```

## ✨ Interactive Features

1. **Hover Effects**
   - Cards lift up (8px) with enhanced shadow
   - Buttons change color with smooth transition
   - Icons change on hover

2. **Drag & Drop**
   - Image upload accepts drag-drop
   - Smooth hover state on drop zone
   - File validation on drop

3. **Image Gallery**
   - Click thumbnails to change main image
   - Smooth image swapping
   - Keyboard support ready

4. **Form Validation**
   - Real-time error messages
   - Visual error states
   - Success confirmations

---

This visual guide helps understand the layout and user experience of the RoomMate app!
