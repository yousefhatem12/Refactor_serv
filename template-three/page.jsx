"use client";
import React from "react";
import WebDevHero from "../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import HostingOffers from "../components/Services-web-hosting/HostingOffers";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export default function TemplateThreePage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-10">
        <WebDevHero
          data={{
            title: "Digital Identity Management",
            bg_image: "/template/template-three-hero.png",
            hidePlanet: true,
            bgPosition: "bg-bottom",
            overlay: "bg-black/20",
          }}
        />

        <WebDesignFeatures
          title="Digital Identity Features from Serve5"
          description=" "
        />

        <div className="py-10">
          <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-10">
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
              Some of our Digital Identity Management at Serve5
            </h2>
          </div>
          <WebDesignPortfolio />
        </div>

        <div className="pt-20">
          <div className="max-w-7xl mx-auto px-6 lg:px-20 text-center mb-[-50px] relative z-30">
            <h2 className="text-white text-3xl md:text-5xl font-bold leading-tight">
              Set up your site, pick a plan later
            </h2>
          </div>
          <HostingOffers />
        </div>

        {/* Split Section 1: Make bright ideas happen */}
        <section className="py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col lg:flex-row items-center gap-12">
          <div className="w-full lg:w-1/2">
            <h2 className="text-white text-3xl md:text-5xl font-bold mb-8 leading-tight">
              Make bright ideas happen.
            </h2>
            <ul className="space-y-4">
              <li className="flex items-center gap-4 text-gray-300">
                <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                  <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                </div>
                <span className="text-lg">Enhance your workflow</span>
              </li>
              <li className="flex items-center gap-4 text-gray-300">
                <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                  <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                </div>
                <span className="text-lg">Master management software</span>
              </li>
              <li className="flex items-center gap-4 text-gray-300">
                <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                  <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                </div>
                <span className="text-lg">Create dynamic business records</span>
              </li>
              <li className="flex items-center gap-4 text-gray-300">
                <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                  <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                </div>
                <span className="text-lg">Reorganize your application</span>
              </li>
              <li className="flex items-center gap-4 text-gray-300">
                <div className="w-6 h-6 rounded-full border border-[#D98B50] flex items-center justify-center shrink-0">
                  <div className="w-2 h-2 bg-[#D98B50] rounded-full"></div>
                </div>
                <span className="text-lg">And much more app capabilities</span>
              </li>
            </ul>
          </div>
          <div className="w-full lg:w-1/2 relative">
            <img
              src="/template/Mask group.png"
              alt="Analytics Dashboard"
              className="w-full h-auto object-contain drop-shadow-2xl"
            />
          </div>
        </section>

        {/* Split Section 2: Build an online presence */}
        <section className="py-20 px-6 lg:px-20 max-w-7xl mx-auto flex flex-col lg:flex-row-reverse items-center gap-12">
          <div className="w-full lg:w-1/2 flex flex-col items-start">
            <h2 className="text-white text-3xl md:text-5xl font-bold mb-6 leading-tight">
              Build an online presence for your business
            </h2>
            <p className="text-gray-400 mb-8 leading-relaxed text-lg">
              Get massive site speed with Serv5 framework, your team has all the
              tools they need to build and maintain an online presence.
            </p>
            <p className="text-sm text-gray-500 uppercase tracking-widest mb-4 font-semibold">
              Trusted by hundreds of clients
            </p>
            <div className="flex flex-col sm:flex-row items-center gap-8">
              <div className="flex -space-x-4">
                <div className="w-12 h-12 rounded-full bg-gray-600 border-2 border-[#03071E]"></div>
                <div className="w-12 h-12 rounded-full bg-gray-500 border-2 border-[#03071E]"></div>
                <div className="w-12 h-12 rounded-full bg-gray-400 border-2 border-[#03071E]"></div>
                <div className="w-12 h-12 rounded-full bg-[#D98B50] border-2 border-[#03071E] flex items-center justify-center text-sm font-bold text-white shadow-lg shadow-[#D98B50]/30">
                  +
                </div>
              </div>
              <button className="px-10 py-4 rounded-full bg-[#D98B50] text-white font-bold hover:bg-orange-600 transition-colors shadow-lg shadow-[#D98B50]/20">
                Get started
              </button>
            </div>
          </div>
          <div className="w-full lg:w-1/2 relative">
            <img
              src="/template/stephen-phillips-hostreviews-co-uk-shr_Xn8S8QU-unsplash 1.png"
              alt="Laptop showing website"
              className="w-full h-auto object-contain drop-shadow-[0_0_40px_rgba(255,255,255,0.05)]"
            />
          </div>
        </section>

        <LatestBlogs />
      </div>
    </main>
  );
}
