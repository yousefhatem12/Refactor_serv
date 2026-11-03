"use client";
import React, { createContext, useContext, useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    // Only check /me if there's an active session marker to avoid 401s on every anonymous visitor
    const hasSession =
      typeof window !== "undefined" &&
      (localStorage.getItem("serv5_has_session") === "1" ||
        document.cookie.includes("serv5_session=") ||
        document.cookie.includes("token="));

    if (!hasSession) {
      setLoading(false);
      return;
    }

    fetchAPI("/me", { credentials: "include" })
      .then((userData) => {
        if (userData) {
          setUser(userData);
          setToken(true);
        } else {
          localStorage.removeItem("serv5_has_session");
        }
      })
      .catch(() => {
        localStorage.removeItem("serv5_has_session");
      })
      .finally(() => setLoading(false));
  }, []);

  const login = async (email, password, turnstileToken) => {
    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1"}/login`, {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({ email, password, turnstile_token: turnstileToken }),
      });
      const data = await response.json();
      if (response.ok) {
        localStorage.setItem("serv5_has_session", "1");
        setToken(true);
        setUser(data.user);
        return { success: true };
      }
      return { success: false, message: data.message || "Login failed" };
    } catch (error) {
      return { success: false, message: "Network error" };
    }
  };

  const register = async (name, email, password, password_confirmation, turnstileToken) => {
    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL || "https://api.serv5.com.eg/api/v1"}/register`, {
        method: "POST",
        credentials: "include",
        headers: { "Content-Type": "application/json", Accept: "application/json" },
        body: JSON.stringify({
          name,
          email,
          password,
          password_confirmation,
          turnstile_token: turnstileToken,
        }),
      });
      const data = await response.json();
      if (response.ok) {
        localStorage.setItem("serv5_has_session", "1");
        setToken(true);
        setUser(data.user);
        return { success: true };
      }
      return { success: false, message: data.message || "Registration failed" };
    } catch (error) {
      return { success: false, message: "Network error" };
    }
  };

  const logout = async () => {
    try {
      await fetchAPI("/logout", { method: "POST", credentials: "include" });
    } finally {
      localStorage.removeItem("serv5_has_session");
      setToken(null);
      setUser(null);
    }
  };

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);
