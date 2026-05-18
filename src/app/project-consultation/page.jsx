"use client";
import React from "react";
import ConsultationHero from "../components/Consultation/ConsultationHero";
import ConsultationForm from "../components/Consultation/ConsultationForm";

export default function ProjectConsultationPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden bg-[#040A37]">
      {/* Background elements consistent with other pages */}
      <div className="fixed inset-0 z-0 pointer-events-none">
        <div className="absolute top-0 right-0 w-[500px] h-[500px] bg-blue-600/10 blur-[150px] rounded-full" />
        <div className="absolute bottom-0 left-0 w-[500px] h-[500px] bg-purple-600/10 blur-[150px] rounded-full" />
      </div>

      <div className="w-full relative z-10">
        <ConsultationHero />
        <ConsultationForm />
      </div>
    </main>
  );
}
