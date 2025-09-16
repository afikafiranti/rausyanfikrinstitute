// import defaultTheme from 'tailwindcss/defaultTheme';
// import forms from '@tailwindcss/forms';

// /** @type {import('tailwindcss').Config} */
// export default {
//     content: [
//         './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
//         './storage/framework/views/*.php',
//         './resources/views/**/*.blade.php',
//     ],

//     theme: {
//         extend: {
//             fontFamily: {
//                 sans: ['Figtree', ...defaultTheme.fontFamily.sans],
//             },
//         },
//     },

//     plugins: [forms],
    
// };
// module.exports = {
//   content: [
//     './resources/views/**/*.blade.php',
//     './resources/js/**/*.js',
//     './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
//   ],
//   safelist: [
//     'bg-green-100','text-green-800',
//     'bg-yellow-100','text-yellow-800',
//     'bg-red-100','text-red-800',
//   ],
//   theme: { extend: {} },
//   plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
// }

// tailwind.config.js (CJS – aman di Laravel + Vite)
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
  ],
  safelist: [
    // badge/alert yang sering dipakai pola Notus
    'bg-green-100','text-green-800',
    'bg-yellow-100','text-yellow-800',
    'bg-red-100','text-red-800',
    'bg-blue-100','text-blue-800',
  ],
  theme: {
    extend: {
      // opsional: nada brand (Notus banyak main di indigo/blue)
      colors: { brand: { DEFAULT: '#4f46e5' } },
      borderRadius: { '2xl': '1rem' },
      boxShadow: { card: '0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1)' },
    },
  },
  plugins: [require('@tailwindcss/forms'), require('@tailwindcss/typography')],
}
