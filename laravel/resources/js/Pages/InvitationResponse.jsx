import React from 'react';
import { Link } from '@inertiajs/react';
import MainLayout from '../Layout/MainLayout';

export default function InvitationResponse({ status, group, email, message }) {
    return (
        <MainLayout>
            <div className="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
                <div className="max-w-md w-full space-y-8">
                    <div className="text-center">
                        {status === 'accepted' ? (
                            <div className="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-green-100 mb-4">
                                <svg className="h-16 w-16 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        ) : (
                            <div className="mx-auto flex items-center justify-center h-24 w-24 rounded-full bg-red-100 mb-4">
                                <svg className="h-16 w-16 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </div>
                        )}
                        
                        <h2 className="mt-6 text-3xl font-extrabold text-gray-900">
                            {status === 'accepted' ? 'Invitation acceptée !' : 'Invitation refusée'}
                        </h2>
                        
                        <div className="mt-4 bg-white shadow-lg rounded-lg p-6">
                            <p className="text-lg text-gray-700">{message}</p>
                            
                            <div className="mt-4 text-sm text-gray-600">
                                <p><strong>Groupe :</strong> {group}</p>
                                <p><strong>Email :</strong> {email}</p>
                            </div>
                        </div>
                        
                        <div className="mt-8">
                            <Link
                                href="/"
                                className="inline-flex items-center px-6 py-3 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500"
                            >
                                Retour à l'accueil
                            </Link>
                        </div>
                    </div>
                </div>
            </div>
        </MainLayout>
    );
}
