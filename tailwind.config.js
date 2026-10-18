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
/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './resources/views/**/*.blade.php',
    './resources/js/**/*.js',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './node_modules/flowbite/**/*.js', // ← tambahkan ini agar komponen Flowbite aktif
  ],
  safelist: [
    'bg-green-100','text-green-800',
    'bg-yellow-100','text-yellow-800',
    'bg-red-100','text-red-800',
    'bg-blue-100','text-blue-800',
  ],
  theme: {
    extend: {
      colors: {
        brand: { DEFAULT: '#284BDA', dark: '#1E3BAA' },
        ink: '#14213D',
        accent: '#C5282F',
        canvas: '#F8F6F1',
        muted: '#536173',
        blueGray: require('tailwindcss/colors').slate,
        latar: '#F8F6F1',
        gelap: '#14213D',
        primary: '#284BDA',
      },
      fontFamily: {
        sans: ['Segoe UI', 'system-ui', 'sans-serif'],
      },
      borderRadius: {
        '2xl': '1rem',
      },
      boxShadow: {
        card: '0 10px 15px -3px rgba(0,0,0,.1), 0 4px 6px -4px rgba(0,0,0,.1)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('flowbite/plugin'), // ← tambahkan ini agar komponen JS Flowbite aktif
  ],
};
