"use client";
import React from "react";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import WebDevHowWeWork from "../components/Services-web-dev/WebDevHowWeWork";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function TemplateOnePage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-10">
        <SocialMediaHero />
        <ServicesFeatures />
        <div className="py-10">
          <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
              Some of our web marketing website work
            </h2>
          </div>
          <PortfolioGallery />
        </div>
        <WebDevHowWeWork />
        <HostingOffers />
        <SocialMediaContact />
        <LatestBlogs />
      </div>
    </main>
  );
}
