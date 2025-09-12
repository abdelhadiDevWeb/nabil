import { defineConfig } from "vite";
import react from "@vitejs/plugin-react";
import { cloudflare } from "@cloudflare/vite-plugin";
import { mochaPlugins } from "@getmocha/vite-plugins";

export default defineConfig({
  base: "/",
  // eslint-disable-next-line @typescript-eslint/no-explicit-any
  plugins: [...mochaPlugins({} as any), react(), cloudflare()],
  server: {
    allowedHosts: true,
  },
  build: {
    chunkSizeWarningLimit: 5000,
  },
  resolve: {
    alias: {
      "@": "./src",
    },
  },
});
