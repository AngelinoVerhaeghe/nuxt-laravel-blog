/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./nuxt.config.{js,ts}",
    "./app.vue",
  ],
  theme: {
    fontFamily: {
      'quicksand': ['Quicksand', 'sans-serif']
    },
    extend: {},
  },
  plugins: [],
}

// Colors
// Sky Cyan Blauw: bg-cyan-300 (#6EE7B7)
// Zachte Hemelblauw: bg-blue-200 (#90CDF4)
// Ijzig Turquoise: bg-teal-300 (#4FD1C5)
// Wolkenwit: bg-gray-100 (#F7FAFC)
// Zeegroen: bg-green-500 (#48BB78)
