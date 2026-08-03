"use client";
import React, { createContext, useContext, useState, useEffect } from "react";
import { fetchAPI } from "@/lib/api";

const AuthContext = createContext();

export const AuthProvider = ({ children }) => {
  const [user, setUser] = useState(null);
  const [token, setToken] = useState(null);
  const [loading, setLoading] = useState(true);

  useEffect(() => {
    const savedToken = localStorage.getItem("auth_token");
    if (savedToken) {
      setToken(savedToken);
      fetchAPI("/me", { 
        headers: { Authorization: `Bearer ${savedToken}` } 
      }).then(userData => {
        if (userData) setUser(userData);
        else logout();
      }).catch(() => logout())
      .finally(() => setLoading(false));
    } else {
      setLoading(false);
    }
  }, []);

  const login = async (email, password) => {
    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL || 'https://api.serv5.com.eg/api/v1'}/login`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ email, password }),
      });
      const data = await response.json();
      if (response.ok) {
        localStorage.setItem("auth_token", data.access_token);
        setToken(data.access_token);
        setUser(data.user);
        return { success: true };
      }
      return { success: false, message: data.message || "Login failed" };
    } catch (error) {
      return { success: false, message: "Network error" };
    }
  };

  const register = async (name, email, password, password_confirmation) => {
    try {
      const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL || 'https://api.serv5.com.eg/api/v1'}/register`, {
        method: "POST",
        headers: { "Content-Type": "application/json", "Accept": "application/json" },
        body: JSON.stringify({ name, email, password, password_confirmation }),
      });
      const data = await response.json();
      if (response.ok) {
        localStorage.setItem("auth_token", data.access_token);
        setToken(data.access_token);
        setUser(data.user);
        return { success: true };
      }
      return { success: false, message: data.message || "Registration failed" };
    } catch (error) {
      return { success: false, message: "Network error" };
    }
  };

  const logout = () => {
    localStorage.removeItem("auth_token");
    setToken(null);
    setUser(null);
  };

  return (
    <AuthContext.Provider value={{ user, token, loading, login, register, logout }}>
      {children}
    </AuthContext.Provider>
  );
};

export const useAuth = () => useContext(AuthContext);