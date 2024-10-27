// astro.config.mjs
import { defineConfig } from 'astro/config';

export default defineConfig({
  vite: {
    build: {
      rollupOptions: {
        external: ['../composants/Header.astro']
      }
    }
  }
});