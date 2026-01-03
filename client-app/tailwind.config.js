/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        // Brand Palette from admin_style.css
        vilela: {
          primary: '#146c43',       // Logo Green (Checkmark/Engenharia)
          dark: '#383838',          // Logo Dark Grey (VILELA)
          accent: '#eab308',        // Logo Window Yellow/Orange
          light: '#f3f4f6',         // Very Light Gray BG (Apple style)
          surface: '#ffffff',       // Pure White
          text: '#1f2937',          // Main Text (Gray-800)
          subtle: '#6b7280',        // Secondary Text (Gray-500)
          border: '#e5e7eb',        // Light Border
        },
        status: {
          success: '#146c43',
          successBg: '#d1e7dd',
          warning: '#f59e0b',
          warningBg: '#fef3c7',
          danger: '#dc2626',
          dangerBg: '#fee2e2',
        }
      },
      fontFamily: {
        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'San Francisco', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
