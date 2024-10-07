/** @type {import('tailwindcss').Config} */
export default {
  content: [
      './storage/framework/views/*.php',
      './resources/views/**/*.blade.php',
      './resources/js/**/*.vue',
  ],
  theme: {
    extend: {},
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],

}

// // tailwind.config.js
// module.exports = {
//   theme: {
//     extend: {
//       fontFamily: {
//         hubballi: ['Hubballi', 'sans-serif'],
//       },
//     },
//   },
//   // other configurations...
// }

