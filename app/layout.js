"use client"; // Ensure the component is rendered on the client side

import { usePathname } from "next/navigation";
import React, { useState, useEffect } from "react";
import Link from "next/link";
import "./globals.css"; // Import global styles if you have them

// Header Component with Logo, Centered Nav, and Hamburger
const Header = ({ toggleSidebar }) => {
  return (
    <header className="bg-gray-900 text-white p-6 flex justify-between items-center">
      <button onClick={toggleSidebar} className="text-2xl">
        <i className="fas fa-bars"></i> {/* Hamburger Icon (Font Awesome) */}
      </button>

      <nav className="flex-grow flex justify-center text-2xl font-serif space-x-14">
        <Link href="/" className="hover:text-indigo-400 transition-colors">
          Home
        </Link>
        <div className="relative group">
          <Link href="/" className="hover:text-indigo-400 transition-colors">
            Academics
          </Link>
          <div className="absolute left-0 mt-2 bg-gray-800 w-48 opacity-0 text-lg group-hover:opacity-100 transform group-hover:translate-x-0 transition-all ease-in-out duration-300 shadow-md invisible group-hover:visible">
            <Link
              href="/subject"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Subject
            </Link>
            <Link
              href="/areas"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Areas
            </Link>
          </div>
        </div>

        <div className="relative group">
          <Link
            href="/"
            className="hover:text-indigo-400 transition-colors"
          >
            Research
          </Link>
          <div className="absolute left-0 mt-2 bg-gray-800 w-48 opacity-0 text-lg group-hover:opacity-100 transform group-hover:translate-x-0 transition-all ease-in-out duration-300 shadow-md invisible group-hover:visible">
            <Link
              href="/current-research"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Current Research
            </Link>
            <Link
              href="/explore-projects"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Explore Projects
            </Link>
          </div>
        </div>

        <div className="relative group">
          <Link
            href=""
            className="hover:text-indigo-400 transition-colors"
          >
            Resources
          </Link>
          <div className="absolute left-0 mt-2 bg-gray-800 w-48 opacity-0 text-lg group-hover:opacity-100 transform group-hover:translate-x-0 transition-all ease-in-out duration-300 shadow-md invisible group-hover:visible">
            <Link
              href="/library"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Library
            </Link>
            <Link
              href="educational-videos"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Educational Videos
            </Link>
            <Link
              href="http://127.0.0.1:8000/"
              className="block text-white py-2 px-4 hover:bg-gray-700"
              target="_blank"
            >
              Educational Content
            </Link>
            <Link
              href="/softwares"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Softwares
            </Link>
          </div>
        </div>

        <div className="relative group">
          <Link
            href=""
            className="hover:text-indigo-400 transition-colors"
          >
            People
          </Link>
          <div className="absolute left-0 mt-2 bg-gray-800 w-48 opacity-0 text-lg group-hover:opacity-100 transform group-hover:translate-x-0 transition-all ease-in-out duration-300 shadow-md invisible group-hover:visible">
            <Link
              href="/faculty"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Faculty
            </Link>
            <Link
              href="/students"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Students
            </Link>
            <Link
              href="/alumni"
              className="block text-white py-2 px-4 hover:bg-gray-700"
            >
              Alumni
            </Link>
          </div>
        </div>
      </nav>

      <div className="flex items-center">
        <img
          src="/images/iitb.png"
          alt="IIT Power Systems Lab Logo"
          className="w-16 h-16"
        />
      </div>
    </header>
  );
};

// Sidebar Component with Main Items (no nested)
const Sidebar = ({ isSidebarOpen, toggleSidebar }) => (
  <div
    className={`fixed inset-0 z-40 bg-gray-800 bg-opacity-75 transition-opacity duration-300 ${
      isSidebarOpen ? "opacity-100" : "opacity-0 pointer-events-none"
    }`}
    onClick={toggleSidebar} // Close the sidebar when clicking outside
  >
    <div
      className={`w-64 bg-gray-900 text-white fixed top-0 left-0 h-full p-6 transition-transform duration-300 ${
        isSidebarOpen ? "transform-none" : "transform -translate-x-full"
      }`}
      onClick={(e) => e.stopPropagation()} // Prevent click from closing sidebar when clicking inside
    >
      <div className="flex justify-end">
        <button onClick={toggleSidebar} className="text-white text-3xl">
          <i className="fas fa-times"></i> {/* Close icon */}
        </button>
      </div>
      <div className="flex flex-col space-y-4">
        <Link
          href="/signup"
          onClick={toggleSidebar}
          className="text-white text-lg hover:text-indigo-400 transition-colors"
        >
          SignUp
        </Link>
        <Link
          href="/login"
          onClick={toggleSidebar}
          className="text-white text-lg hover:text-indigo-400 transition-colors"
        >
          Login
        </Link>
        <Link
          href="/lab-members"
          onClick={toggleSidebar}
          className="text-white text-lg hover:text-indigo-400 transition-colors"
        >
          Lab Members
        </Link>
      </div>
    </div>
  </div>
);


// Footer Component with Social Links
const Footer = () => (
  <footer className="bg-gray-800 text-white py-8 mt-auto">
    <div className="container mx-auto text-center">
      <div className="space-x-6 mb-4">
        <a
          href="https://www.youtube.com/@pslab_iitb"
          target="_blank"
          rel="noopener noreferrer"
          className="hover:text-indigo-400"
        >
          <i className="fab fa-youtube"></i> Youtube
        </a>
        <a
          href="https://github.com"
          target="_blank"
          rel="noopener noreferrer"
          className="hover:text-indigo-400"
        >
          <i className="fab fa-github"></i> GitHub
        </a>
      </div>
      <p className="text-sm">
        &copy; 2025 IIT Power Systems Lab. All rights reserved.
      </p>
    </div>
  </footer>
);

export default function Layout({ children }) {
  const [isSidebarOpen, setSidebarOpen] = useState(false);
  const pathname = usePathname();

  const hideLayout = [
    "/projects",
    "/lab-resources",
    "/home",
    "/books",
    "/equipments",
    "/resources",
    "/tasks",
    "/tools",
    "/homepage",
    "/all-projects",
    "/trial",
    "/login-request",
    "/all-task",
    "/reserved-equip",
    "/meeting",
    "/upmath",
    "/nextcloud",
    "/educational-videos",
    "/excalidraw",
    "/tawk",
    "/overleaf",

  ].includes(pathname);

  const closeSidebar = () => setSidebarOpen(false);
  const toggleSidebar = () => setSidebarOpen(!isSidebarOpen);

  useEffect(() => {
    const handleScroll = () => closeSidebar();
    window.addEventListener("scroll", handleScroll);
    return () => {
      window.removeEventListener("scroll", handleScroll);
    };
  }, []);

  return (
    <html lang="en">
      <head>
        <title>IIT Power Systems Lab</title>
        <link
          rel="stylesheet"
          href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"
        />
        <link rel="icon" type="image/png" href="/images/iitb.png" />
      </head>
      <body className="font-sans bg-gray-100 text-gray-900">
        <div className="flex min-h-screen flex-col">
          <Sidebar isSidebarOpen={isSidebarOpen} toggleSidebar={toggleSidebar} />
          <div>
            {!hideLayout && <Header toggleSidebar={toggleSidebar}/>}
            <main>{children}</main>
            {!hideLayout && <Footer />}
          </div>
        </div>
      </body>
    </html>
  );
}
