<?php

namespace App\Services;

use App\Models\Signature;
use App\Models\User;
use Illuminate\Support\Str;

class SignatureService
{
    /**
     * Verify the validity of a digital signature
     *
     * @param Signature $signature
     * @return bool
     */
    public function verifySignature(Signature $signature)
    {
        // In a real implementation, we would verify the digital signature
        // using cryptographic algorithms. For this example, we simply
        // check that it exists.
        return !empty($signature->digital_signature);
    }
    
    /**
     * Generate a token for digital signature
     *
     * @param User $user
     * @return string
     */
    public function generateSignatureToken(User $user)
    {
        // In a real implementation, this token could be sent by email
        // or SMS as a second authentication factor for the signature.
        return Str::random(6);
    }
    
    /**
     * Generate metadata information for the signature
     *
     * @param User $user
     * @param string $signatureType
     * @return array
     */
    public function generateSignatureMetadata(User $user, $signatureType)
    {
        return [
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'position' => $user->position,
            'signature_type' => $signatureType,
            'timestamp' => now()->timestamp,
            'ip' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ];
    }
}