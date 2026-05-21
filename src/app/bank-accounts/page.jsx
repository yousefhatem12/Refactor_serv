"use client";
import React from "react";
import BankAccountsContent from "../components/BankAccounts/BankAccountsContent";

export default function BankAccountsPage() {
  return (
    <main className="flex min-h-screen flex-col relative overflow-x-hidden">
      <div className="w-full relative z-10 pb-10 pt-32 animate-fade-in">
        <BankAccountsContent />
      </div>
    </main>
  );
}
