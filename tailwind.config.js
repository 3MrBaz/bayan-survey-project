import defaultTheme from 'tailwindcss/defaultTheme'
import forms from '@tailwindcss/forms'
import daisyui from 'daisyui'

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {},
  },
  plugins: [forms, daisyui],
}
