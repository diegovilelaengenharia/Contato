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
          primary: '#146c43',       // Dark Green
          light: '#d1e7dd',         // Light Green BG
          bg: '#f0f8f5',            // App Background (Mint/Gray)
          surface: '#ffffff',       // Card Background
          text: '#2f3e36',          // Dark Greenish Text
          subtle: '#5f7a6c',        // Muted Text
          border: '#dbece5',        // Border Color
        },
        status: {
          success: '#198754',
          successBg: '#d4edda',
          warning: '#856404',
          warningBg: '#fff3cd',
          danger: '#dc3545',
          dangerBg: '#f8d7da',
        }
      },
      fontFamily: {
        sans: ['Inter', '-apple-system', 'BlinkMacSystemFont', 'San Francisco', 'Segoe UI', 'Roboto', 'Helvetica Neue', 'sans-serif'],
      }
    },
  },
  plugins: [],
}
