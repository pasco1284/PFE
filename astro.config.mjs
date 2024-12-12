import { defineConfig } from 'astro/config';

export default defineConfig({
  // autres options...
  resolve: {
    alias: {
      '@composants': '/src/pages/composants',
    },
  },
});