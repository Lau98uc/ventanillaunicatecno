import defaultTheme from 'tailwindcss/defaultTheme'


export default {
  content: ['./resources/**/*.{js,vue,blade.php}'],
  theme: {
    extend: {
      colors: {
        indigo: {
          100: '#e6e8ff',
          300: '#b2b7ff',
          400: '#7886d7',
          500: '#6574cd',
          600: '#5661b3',
          800: '#2f365f',
          900: '#191e38',
        },
      },
      fontFamily: {
        sans: ['"Cerebri Sans"', ...defaultTheme.fontFamily.sans],
      },
    },
    },
   plugins: [
    require('daisyui'),
    ],
   daisyui: {
       themes: [
            "light",
            "dark",
            "cupcake",
            "bumblebee",
            "emerald",
            "corporate",
            "synthwave",
            "retro",
           "cyberpunk",
            "valentine",
            "halloween",
            "garden",
            "forest",
           "aqua", {
               'lofi': {
                   'primary': '#808080',
                   'primary-focus': '#737373',
                   'primary-content': '#f2f2f3',

                   'secondary': '#4d4d4d',
                   'secondary-focus': '#404040',
                   'secondary-content': '#f2f2f3',

                   'accent': '#1a1a1a',
                   'accent-focus': '#0d0d0d',
                   'accent-content': '#f2f2f3',

                   'neutral': '#f2f2f3',
                   'neutral-focus': '#e4e4e7',
                   'neutral-content': '#4d4d4d',

                   'base-100': '#ffffff',
                   'base-200': '#ffffff',
                   'base-300': '#ffffff',
                   'base-content': '#7d7d7d',

                   'info': '#1c92f2',
                   'success': '#009485',
                   'warning': '#ff9900',
                   'error': '#ff5724',

                   '--rounded-box': '0',
                   '--rounded-btn': '0',
                   '--rounded-badge': '0',

                   '--animation-btn': '0',
                   '--animation-input': '0',

                   '--btn-text-case': 'uppercase',
                   '--navbar-padding': '.5rem',
                   '--border-btn': '1px',
               },
           },
            "pastel",
            "fantasy",
            "wireframe",
            "black",
            "luxury",
            "dracula",
            "cmyk",
            "autumn",
            "business",
            "acid",
            "lemonade",
            "night",
            "coffee",
            "winter",
        ],
    },
}
