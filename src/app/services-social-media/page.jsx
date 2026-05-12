import React from "react";

import SocialMediaHero from "../components/Services-social-media/SocialMediaHero";
import DigitalMarketingServices from "../components/Services-social-media/DigitalMarketingServices";
import SocialMediaWhyChooseUs from "../components/Services-social-media/SocialMediaWhyChooseUs";
import SocialMediaHowWeWork from "../components/Services-social-media/SocialMediaHowWeWork";
import SocialMediaContact from "../components/Services-social-media/SocialMediaContact";
import LatestBlogs from "../components/Services-web-hosting/LatestBlogs";

export const metadata = {
  title: "Social Media Services | Serv5",
  description: "Get the best social media services for your business.",
};

export default function SocialMediaPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-20">
        <SocialMediaHero />
        <DigitalMarketingServices />
        <SocialMediaWhyChooseUs />
        <SocialMediaHowWeWork />
        <SocialMediaContact />
        <LatestBlogs />
      </div>
    </main>
  );
}
