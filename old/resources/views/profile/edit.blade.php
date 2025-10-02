@extends('layouts.master')
@section('title','profile')
@section('content')
    <div class="py-8">
        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Profile Navigation -->
            <div class="mb-8">
                <nav class="flex space-x-4" aria-label="Profile navigation">
                    <a href="#profile-info" class="px-3 py-2 font-medium text-sm rounded-md bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-100">
                        {{ __('Profile Information') }}
                    </a>
                    <a href="#password" class="px-3 py-2 font-medium text-sm rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('Update Password') }}
                    </a>
                    <a href="#delete-account" class="px-3 py-2 font-medium text-sm rounded-md text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                        {{ __('Delete Account') }}
                    </a>
                </nav>
            </div>

            <div class="space-y-6">
                <!-- Profile Information Section -->
                <div id="profile-info" class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center mb-6">
                            <div class="mr-4">
                                <div class="w-16 h-16 rounded-full bg-indigo-100 dark:bg-indigo-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-indigo-600 dark:text-indigo-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Profile Information') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Update your account\'s profile information and email address.') }}</p>
                            </div>
                        </div>
                        <div class="max-w-xl">
                            @include('profile.partials.update-profile-information-form')
                        </div>
                    </div>
                </div>

                <!-- Update Password Section -->
                <div id="password" class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center mb-6">
                            <div class="mr-4">
                                <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-blue-600 dark:text-blue-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Update Password') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Ensure your account is using a long, random password to stay secure.') }}</p>
                            </div>
                        </div>
                        <div class="max-w-xl">
                            @include('profile.partials.update-password-form')
                        </div>
                    </div>
                </div>

                <!-- Delete Account Section -->
                <div id="delete-account" class="bg-white dark:bg-gray-800 shadow-xl rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-2xl">
                    <div class="p-6 sm:p-8">
                        <div class="flex items-center mb-6">
                            <div class="mr-4">
                                <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900 flex items-center justify-center">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-red-600 dark:text-red-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white">{{ __('Delete Account') }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('Permanently delete your account.') }}</p>
                            </div>
                        </div>
                        <div class="max-w-xl">
                            @include('profile.partials.delete-user-form')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('styles')
    <style>
        /* Smooth scrolling for anchor links */
        html {
            scroll-behavior: smooth;
        }

        /* Transition effects */
        .transition-all {
            transition-property: all;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Dark mode transitions */
        .dark .bg-white {
            transition: background-color 0.3s ease;
        }

        /* Card hover effects */
        .hover\:shadow-2xl:hover {
            --tw-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            box-shadow: var(--tw-ring-offset-shadow, 0 0 #0000), var(--tw-ring-shadow, 0 0 #0000), var(--tw-shadow);
        }

        /* Form input focus styles */
        .focus\:ring-indigo-500:focus {
            --tw-ring-color: rgba(99, 102, 241, 0.5);
        }
    </style>
@endpush
@push('scripts')
    <script>
        // Highlight active navigation item
        document.addEventListener('DOMContentLoaded', function() {
            const navItems = document.querySelectorAll('[aria-label="Profile navigation"] a');

            navItems.forEach(item => {
                item.addEventListener('click', function() {
                    navItems.forEach(nav => {
                        nav.classList.remove('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900', 'dark:text-indigo-100');
                        nav.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
                    });

                    this.classList.add('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900', 'dark:text-indigo-100');
                    this.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
                });
            });

            // Highlight based on current hash
            function highlightCurrentSection() {
                const hash = window.location.hash;
                if (!hash) return;

                const targetItem = document.querySelector(`[href="${hash}"]`);
                if (targetItem) {
                    navItems.forEach(nav => {
                        nav.classList.remove('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900', 'dark:text-indigo-100');
                        nav.classList.add('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
                    });

                    targetItem.classList.add('bg-indigo-100', 'text-indigo-700', 'dark:bg-indigo-900', 'dark:text-indigo-100');
                    targetItem.classList.remove('text-gray-500', 'hover:text-gray-700', 'dark:text-gray-400', 'dark:hover:text-gray-200');
                }
            }

            // Initial highlight
            highlightCurrentSection();

            // Update on hash change
            window.addEventListener('hashchange', highlightCurrentSection);
        });
    </script>
@endpush
