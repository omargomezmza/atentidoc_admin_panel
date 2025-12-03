/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  theme: {
    extend: {
      colors: {
        'atenti-green': '#1CD8D2',
        'atenti-blue': '#2684FF',
      },
    },
  },
  plugins: [],
}