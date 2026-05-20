"use client";
import React from "react";
import Image from "next/image";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import ServicesFeatures from "../components/Services-web-hosting/ServicesFeatures";
import PortfolioGallery from "../components/Portfolio/PortfolioGallery";
import SocialMediaHowWeWork from "../components/Services-social-media/SocialMediaHowWeWork";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function TemplateOnePage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-10">
        <SocialMediaHero 
          title="Network Marketing"
          desc="Network marketing is a business model that depends on person-to-person sales by independent representatives, often working from home. A network marketing business may require you to build a network of business partners or salespeople to assist with lead generation and closing sales."
          bgImage="/template/template-one-hero.png"
          planetImage={null}
        />
        <ServicesFeatures 
          title="Advantages of a web marketing website from serv5"
          featuresData={[
            {
              id: 1,
              title: "Network marketing strategy",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><path d="M19 16.9A5 5 0 0 0 18 7h-1.26a8 8 0 1 0-11.62 9" /><polyline points="13 11 9 17 15 17 11 23" /></svg>
              ),
            },
            {
              id: 2,
              title: "Targeted audience engagement",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><rect x="2" y="4" width="20" height="16" rx="2" /><path d="M6 8h.01" /><path d="M10 8h.01" /><path d="M14 8h.01" /><path d="M2 12h20" /><path d="M12 16v.01" /><path d="M16 16v.01" /><path d="M8 16v.01" /></svg>
              ),
            },
            {
              id: 3,
              title: "Social media management",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z" /><path d="M3.27 6.96L12 12.01l8.73-5.05" /><path d="M12 22.08V12" /><circle cx="8" cy="10" r="1" fill="currentColor" /><circle cx="16" cy="14" r="1" fill="currentColor" /></svg>
              ),
            },
            {
              id: 4,
              title: "Content creation and distribution",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><circle cx="12" cy="12" r="10" /><line x1="2" y1="12" x2="22" y2="12" /><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" /></svg>
              ),
            },
            {
              id: 5,
              title: "Performance tracking and analytics",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z" /><path d="m9 12 2 2 4-4" /></svg>
              ),
            },
            {
              id: 6,
              title: "Continuous optimization",
              desc: "The main center for the server remains the space because the servers are linked to providing a response to commands as quickly as possible.",
              icon: (
                <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" className="text-[#D98B50]"><path d="M12 2l4.5 2.5v5L12 12l-4.5-2.5v-5L12 2z" /><path d="M16.5 9.5l4.5 2.5v5L16.5 19.5l-4.5-2.5v-5l4.5-2.5z" /><path d="M7.5 9.5l4.5 2.5v5L7.5 19.5l-4.5-2.5v-5l4.5-2.5z" /></svg>
              ),
            },
          ]}
        />
        <div className="py-10">
          <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
              Some of our web marketing website work
            </h2>
          </div>
          <div className="max-w-7xl mx-auto px-6 lg:px-20">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              {[1, 2, 3, 4, 5, 6].map((item) => (
                <div key={item} className="relative group overflow-hidden rounded-xl aspect-[1.6/1] bg-[#ffffff10] border border-[#ffffff20]">
                  <Image
                    src={`/portfolio/image-${item}.png`}
                    alt={`Portfolio Work ${item}`}
                    fill
                    className="object-cover transition-transform duration-500 group-hover:scale-110"
                  />
                </div>
              ))}
            </div>
          </div>
        </div>
        <SocialMediaHowWeWork />
        <HostingOffers title="WPR Marketing Offers" />
        <SocialMediaContact />
        <LatestBlogs />
      </div>
    </main>
  );
}
