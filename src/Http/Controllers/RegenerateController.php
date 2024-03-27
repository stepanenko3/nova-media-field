<?php

namespace Stepanenko3\NovaMediaField\Http\Controllers;

use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RegenerateController
{
    public function __invoke(
        Request $request,
        mixed $fileManipulator,
    ): JsonResponse {
        try {
            $media = config('media-library.media_model')::findOrFail(
                $request->route('id'),
            );

            $fileManipulator->createDerivedFiles(
                $media,
            );

            return response()->json([
                'success' => true,
            ]);
        } catch (Exception) {
            return response()->json([
                'success' => false,
                'message' => 'Something went wrong',
            ]);
        }
    }
}
