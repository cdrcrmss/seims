import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    darkMode: 'class',
    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                inter: ['Inter', ...defaultTheme.fontFamily.sans],
                poppins: ['Poppins', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // SPUP Brand Colors - Unified with Login
                'spup-green': {
                    DEFAULT: '#16a34a',
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#16a34a', // Primary SPUP Green (Login match)
                    600: '#15803d',
                    700: '#166534',
                    800: '#14532d',
                    900: '#052e16',
                },
                // Secondary SPUP Green (Legacy support)
                'spup-dark': {
                    DEFAULT: '#006633',
                    50: '#e6f2ed',
                    100: '#cce6db',
                    200: '#99ccb8',
                    300: '#66b394',
                    400: '#339971',
                    500: '#006633',
                    600: '#005229',
                    700: '#003d1f',
                    800: '#002914',
                    900: '#00140a',
                },
                'spup-gold': {
                    DEFAULT: '#FFCC00',
                    50: '#fff9e6',
                    100: '#fff3cc',
                    200: '#ffe799',
                    300: '#ffdb66',
                    400: '#ffcf33',
                    500: '#FFCC00', // Primary SPUP Gold
                    600: '#cca300',
                    700: '#997a00',
                    800: '#665200',
                    900: '#332900',
                },
                // Keep primary/secondary for legacy support
                primary: {
                    50: '#e6f2ed',
                    100: '#cce6db',
                    200: '#99ccb8',
                    300: '#66b394',
                    400: '#339971',
                    500: '#006633',
                    600: '#005229',
                    700: '#003d1f',
                    800: '#002914',
                    900: '#00140a',
                },
                secondary: {
                    50: '#fff9e6',
                    100: '#fff3cc',
                    200: '#ffe799',
                    300: '#ffdb66',
                    400: '#ffcf33',
                    500: '#FFCC00',
                    600: '#cca300',
                    700: '#997a00',
                    800: '#665200',
                    900: '#332900',
                },
            },
            backdropBlur: {
                xs: '2px',
                '4xl': '72px',
            },
            backgroundImage: {
                'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
                'gradient-conic': 'conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))',
                'hero-pattern': "url(\"data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%239C92AC' fill-opacity='0.05'%3E%3Ccircle cx='30' cy='30' r='30'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E\")",
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-in-out',
                'fade-in-up': 'fadeInUp 0.5s ease-out',
                'fade-in-down': 'fadeInDown 0.5s ease-out',
                'slide-up': 'slideUp 0.3s ease-out',
                'slide-down': 'slideDown 0.3s ease-out',
                'slide-left': 'slideLeft 0.3s ease-out',
                'slide-right': 'slideRight 0.3s ease-out',
                'scale-in': 'scaleIn 0.2s ease-out',
                'scale-out': 'scaleOut 0.2s ease-in',
                'bounce-in': 'bounceIn 0.6s ease-out',
                'flip': 'flip 0.6s ease-in-out',
                'glow': 'glow 2s ease-in-out infinite',
                'pulse-slow': 'pulse 3s cubic-bezier(0.4, 0, 0.6, 1) infinite',
                'float': 'float 6s ease-in-out infinite',
                'shimmer': 'shimmer 2s linear infinite',
                'wobble': 'wobble 1s ease-in-out',
                'shake': 'shake 0.5s ease-in-out',
                'heartbeat': 'heartbeat 1.5s ease-in-out infinite',
                'typewriter': 'typewriter 3.5s steps(30, end) infinite',
                'gradient': 'gradient 8s ease infinite',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                fadeInUp: {
                    '0%': { opacity: '0', transform: 'translateY(20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                fadeInDown: {
                    '0%': { opacity: '0', transform: 'translateY(-20px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideUp: {
                    '0%': { transform: 'translateY(10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideDown: {
                    '0%': { transform: 'translateY(-10px)', opacity: '0' },
                    '100%': { transform: 'translateY(0)', opacity: '1' },
                },
                slideLeft: {
                    '0%': { transform: 'translateX(10px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                slideRight: {
                    '0%': { transform: 'translateX(-10px)', opacity: '0' },
                    '100%': { transform: 'translateX(0)', opacity: '1' },
                },
                scaleIn: {
                    '0%': { transform: 'scale(0.9)', opacity: '0' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                scaleOut: {
                    '0%': { transform: 'scale(1)', opacity: '1' },
                    '100%': { transform: 'scale(0.9)', opacity: '0' },
                },
                bounceIn: {
                    '0%': { transform: 'scale(0.3)', opacity: '0' },
                    '50%': { transform: 'scale(1.05)', opacity: '0.7' },
                    '70%': { transform: 'scale(0.9)', opacity: '0.9' },
                    '100%': { transform: 'scale(1)', opacity: '1' },
                },
                flip: {
                    '0%': { transform: 'perspective(400px) rotateY(0)' },
                    '40%': { transform: 'perspective(400px) translateZ(150px) rotateY(170deg)', animationTimingFunction: 'ease-out' },
                    '50%': { transform: 'perspective(400px) translateZ(150px) rotateY(190deg) scale(1)', animationTimingFunction: 'ease-in' },
                    '80%': { transform: 'perspective(400px) rotateY(360deg) scale(0.95)', animationTimingFunction: 'ease-in' },
                    '100%': { transform: 'perspective(400px) scale(1)', animationTimingFunction: 'ease-in' },
                },
                glow: {
                    '0%, 100%': { boxShadow: '0 0 20px rgba(59, 130, 246, 0.3)' },
                    '50%': { boxShadow: '0 0 30px rgba(59, 130, 246, 0.5), 0 0 40px rgba(147, 51, 234, 0.3)' },
                },
                float: {
                    '0%, 100%': { transform: 'translateY(0px)' },
                    '50%': { transform: 'translateY(-10px)' },
                },
                shimmer: {
                    '0%': { backgroundPosition: '-200px 0' },
                    '100%': { backgroundPosition: '200px 0' },
                },
                wobble: {
                    '0%': { transform: 'translateX(0%)' },
                    '15%': { transform: 'translateX(-25%) rotate(-5deg)' },
                    '30%': { transform: 'translateX(20%) rotate(3deg)' },
                    '45%': { transform: 'translateX(-15%) rotate(-3deg)' },
                    '60%': { transform: 'translateX(10%) rotate(2deg)' },
                    '75%': { transform: 'translateX(-5%) rotate(-1deg)' },
                    '100%': { transform: 'translateX(0%)' },
                },
                shake: {
                    '0%, 100%': { transform: 'translateX(0)' },
                    '10%, 30%, 50%, 70%, 90%': { transform: 'translateX(-10px)' },
                    '20%, 40%, 60%, 80%': { transform: 'translateX(10px)' },
                },
                heartbeat: {
                    '0%': { transform: 'scale(1)' },
                    '14%': { transform: 'scale(1.3)' },
                    '28%': { transform: 'scale(1)' },
                    '42%': { transform: 'scale(1.3)' },
                    '70%': { transform: 'scale(1)' },
                },
                typewriter: {
                    '0%': { width: '0ch' },
                    '50%': { width: '15ch' },
                    '100%': { width: '0ch' },
                },
                gradient: {
                    '0%, 100%': { backgroundPosition: '0% 50%' },
                    '50%': { backgroundPosition: '100% 50%' },
                },
            },
            boxShadow: {
                'glow': '0 0 20px rgba(59, 130, 246, 0.3)',
                'glow-lg': '0 0 30px rgba(59, 130, 246, 0.4)',
                'glow-xl': '0 0 40px rgba(59, 130, 246, 0.5)',
                'inner-glow': 'inset 0 2px 4px 0 rgba(59, 130, 246, 0.1)',
                'soft': '0 2px 15px -3px rgba(0, 0, 0, 0.07), 0 10px 20px -2px rgba(0, 0, 0, 0.04)',
                'glass': '0 8px 32px 0 rgba(31, 38, 135, 0.37)',
            },
            blur: {
                'xs': '2px',
                '4xl': '72px',
                '5xl': '96px',
            },
            scale: {
                '102': '1.02',
                '103': '1.03',
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
    ],
};