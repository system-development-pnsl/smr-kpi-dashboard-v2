/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ['"DM Sans"', 'ui-sans-serif', 'system-ui', 'sans-serif'],
                mono: ['"DM Mono"', 'monospace'],
            },
            colors: {
                brand: {
                    black:   '#0A0A0A',
                    white:   '#FAFAFA',
                    bg:      '#F5F5F3',
                    surface: '#FFFFFF',
                    border:  '#E5E5E3',
                    muted:   '#737373',
                    subtle:  '#A3A3A3',
                },
                status: {
                    green:      '#16A34A',
                    'green-bg': '#DCFCE7',
                    amber:      '#D97706',
                    'amber-bg': '#FEF3C7',
                    red:        '#DC2626',
                    'red-bg':   '#FEE2E2',
                    blue:       '#2563EB',
                    'blue-bg':  '#DBEAFE',
                },
            },
            boxShadow: {
                card:       '0 1px 3px 0 rgb(0 0 0 / 0.06), 0 1px 2px -1px rgb(0 0 0 / 0.04)',
                'card-hover':'0 4px 12px 0 rgb(0 0 0 / 0.08), 0 2px 4px -2px rgb(0 0 0 / 0.04)',
            },
        },
    },
    plugins: [],
}
