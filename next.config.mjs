/** @type {import('next').NextConfig} */
const nextConfig = {
  // Keep all generated metadata in the document <head> for every user agent.
  htmlLimitedBots: /.*/,
  poweredByHeader: false,
  experimental: {
    optimizePackageImports: ["react-icons"],
  },
  images: {
    unoptimized: false,
    remotePatterns: [
      {
        protocol: "https",
        hostname: "serv5.com",
      },
      {
        protocol: "https",
        hostname: "www.serv5.com",
      },
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
