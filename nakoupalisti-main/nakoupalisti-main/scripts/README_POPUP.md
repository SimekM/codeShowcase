# Popup Component Setup Guide

This guide explains how to set up and use the improved popup component.

## Features

- Modern, styled popup with animation effects
- Dismissible via close button (X), footer button, clicking outside, or pressing ESC
- Easy to toggle on/off through the editor interface
- Fully responsive design
- Prevents background scrolling when popup is active

## Setup Steps

1. **Add Translation Keys to Database**

   The popup uses the following keys from the database:

   - Key 609: Popup heading text
   - Key 610: Popup message content
   - Key 613: Toggle value ("true" to display popup, "false" to hide it)

2. **Use the Editor to Modify Content**

   You can easily manage the popup through the Editor interface:

   - Go to `/editor`
   - At the top of the form, you'll find a checkbox to enable/disable the popup
   - Below that, you can edit the popup heading and content
   - Click "Save All" to apply your changes

## How It Works

- The popup appears automatically on page load when enabled
- Users can close it by:
  - Clicking the X button in the top-right corner
  - Clicking the "Close" button at the bottom
  - Clicking anywhere outside the popup
  - Pressing the ESC key

## Customizing the Popup Style

To customize the popup appearance, edit:
`app/Components/Ui/Popup/assets.latte`

The popup uses CSS variables from your theme:

- `--primary-variant`: Background color
- `--secondary-color`: Text and button color
- `--secondary-variant`: Hover effects

## Programmatically Controlling the Popup

You can show or hide the popup programmatically with JavaScript:

```javascript
// Show the popup
popupSystem.showPopup();

// Hide the popup
popupSystem.hidePopup();
```
