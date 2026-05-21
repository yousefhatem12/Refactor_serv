"use client";
import React from "react";
import WebDevHero from "../components/Services-web-dev/WebDevHero";
import WebDesignFeatures from "../components/Services-web-design/WebDesignFeatures";
import WebDesignPortfolio from "../components/Services-web-design/WebDesignPortfolio";
import MobileAppHowWeWork from "../components/Services-web-dev/MobileAppHowWeWork";
import WebDesignRequest from "../components/Services-web-design/WebDesignRequest";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";
import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";

export default function TemplateTwoPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden ">
      <div className="w-full relative z-10 pb-10 pt-10">
        <SocialMediaHero
          title="Mobile Application Development"
          desc="If your dream is to own a website with distinctive design that remains in your place with servers, for we will ... an integrated team is available that work to provide the best web design service with the latest technology. If your dream is to own a website with distinctive design that remains in your place with servers, for we will ... an integrated team is available that work to provide the best ..."
          bgImage="/template/template-two-hero.png"
          planetImage={null}
        />
        <WebDesignFeatures
          title="What distinguish us at Mobile app development?"
          description=" "
        />
        <WebDesignPortfolio isMobileApp={true} />
        <MobileAppHowWeWork />
        <WebDesignRequest />
        <LatestBlogs />
      </div>
    </main>
  );
}
