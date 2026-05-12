import React from "react";

import ServicesHero from "../components/Services-web-hosting/ServicesHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export const metadata = {
  title: "Services | Serv5",
  description: "Discover our powerful hosting services and features.",
};

export default function ServicesPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        <ServicesHero />
        <ServicesFeatures />
        <HostingOffers />
        <LatestBlogs />
      </div>
    </main>
  );
}
