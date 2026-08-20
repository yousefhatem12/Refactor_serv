/** @type {import('next').NextConfig} */
const nextConfig = {
  cacheComponents: true,
  // NOTE: htmlLimitedBots: /.*/ was removed because it conflicts with PPR (cacheComponents).
  // With PPR, the static shell is built with __next_metadata_boundary__ placeholders for
  // streaming metadata. Setting htmlLimitedBots: /.*/ forces blocking metadata for ALL
  // requests, which mismatches the pre-built static shell → React error #419.
  // Next.js default bot list already covers Google, Bing, Twitter, Slack crawlers.
  poweredByHeader: false,
  images: {
    unoptimized: false,
    remotePatterns: [
      {
        protocol: "https",
        hostname: "api.serv5.com.eg",
      },
      {
        protocol: "http",
        hostname: "localhost",
        port: "8000",
      },
      {
        protocol: "http",
        hostname: "127.0.0.1",
        port: "8000",
      },
    ],
  },
};

export default nextConfig;
