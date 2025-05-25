<?php

namespace App\Http\Controllers\Clients;

use App\Helper\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\Client;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ClientController extends Controller
{
    // Create a new client Start ***************************
    public function createClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients',
            'contact_person' => 'nullable|string|max:255',
        ]);
        try {
            Client::create([
                'user_id' => Auth::user()->id,
                'name' => $request->name,
                'email' => $request->email,
                'contact_person' => $request->contact_person,
            ]);

            return ResponseHelper::Out(true, 'Client Created Successfully', 200);
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Client Creation Failed, Please Try Again.', 500);
        }
    }
    // Create a new client End ***************************

    // Get all clients Start ***************************
    public function getClients(Request $request)
    {
        try {
            $clients = Client::where('user_id', Auth::user()->id)->get();

            return ResponseHelper::Out(true, $clients, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to Retrieve Clients, Please Try Again.', 500);
        }
    }
    // Get all clients End ***************************

    // Get a single client Start ***************************
    public function getClient(Request $request)
    {
        try {
            $client = Client::where('user_id', Auth::user()->id)->where('id', $request->id)->first();
            if (! $client) {
                return ResponseHelper::Out(false, 'Client Not Found', 404);
            }

            return ResponseHelper::Out(true, $client, 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to Retrieve Client, Please Try Again.', 500);
        }
    }
    // Get a single client End ***************************

    // Update a client Start ***************************
    public function updateClient(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:clients,email,'.$request->id,
            'contact_person' => 'nullable|string|max:255',
        ]);
        try {
            $client = Client::where('user_id', Auth::user()->id)->where('id', $request->id)->first();
            if (! $client) {
                return ResponseHelper::Out(false, 'Client Not Found', 404);
            }
            $client->update([
                'name' => $request->name,
                'email' => $request->email,
                'contact_person' => $request->contact_person,
            ]);

            return ResponseHelper::Out(true, 'Client Updated Successfully', 200);
        } catch (ValidationException $e) {
            return ResponseHelper::Out(false, $e->errors(), 422);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Client Update Failed, Please Try Again.', 500);
        }
    }
    // Update a client End ***************************

    // Delete a client Start ***************************
    public function deleteClient(Request $request)
    {
        try {
            $client = Client::where('user_id', Auth::user()->id)->where('id', $request->id)->first();
            if (! $client) {
                return ResponseHelper::Out(false, 'Client Not Found', 404);
            }
            $client->delete();

            return ResponseHelper::Out(true, 'Client Deleted Successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Client Deletion Failed, Please Try Again.', 500);
        }
    }
    // Delete a client End ***************************

    // Delete all clients Start ***************************
    public function deleteAllClients(Request $request)
    {
        try {
            $deleted = Client::where('user_id', Auth::id())->delete();
            if (! $deleted) {
                return ResponseHelper::Out(false, 'No Clients Found to Delete', 404);
            }

            return ResponseHelper::Out(true, 'All Clients Deleted Successfully', 200);
        } catch (Exception $e) {
            return ResponseHelper::Out(false, 'Failed to Delete Clients, Please Try Again.', 500);
        }
    }
    // Delete all clients End ***************************
}
