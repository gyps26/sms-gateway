<?php
/*
 * Copyright © 2018-2025 RBSoft (Ravi Patel). All rights reserved.
 *
 * Author: Ravi Patel
 * Website: https://rbsoft.org/downloads/sms-gateway
 *
 * This software is licensed, not sold. Buyers are granted a limited, non-transferable license
 * to use this software exclusively on a single domain, subdomain, or computer. Usage on
 * multiple domains, subdomains, or computers requires the purchase of additional licenses.
 *
 * Redistribution, resale, sublicensing, or sharing of the source code, in whole or in part,
 * is strictly prohibited. Modification (except for personal use by the licensee), reverse engineering,
 * or creating derivative works based on this software is strictly prohibited.
 *
 * Unauthorized use, reproduction, or distribution of this software may result in severe civil
 * and criminal penalties and will be prosecuted to the fullest extent of the law.
 *
 * For licensing inquiries or support, please visit https://support.rbsoft.org.
 */

namespace App\Http\Controllers;

use App\Data\ImportContactsData;
use App\Data\UnsubscribeContactData;
use App\DataTable;
use App\Enums\FieldType;
use App\Exports\ContactsExport;
use App\Helpers\Environment;
use App\Http\Requests\StoreContactRequest;
use App\Http\Requests\UpdateContactRequest;
use App\Http\Resources\ContactResource;
use App\Imports\ContactsImport;
use App\Models\Contact;
use App\Models\ContactList;
use App\Monitor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class ContactController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(ContactList $contactList): Response|RedirectResponse
    {
        return DataTable::make($contactList->contacts())
                        ->search(fn($query, $search) => $query->search($search))
                        ->sort(['mobile_number', 'subscribed', 'created_at', 'updated_at'])
                        ->render('ContactLists/Show', fn($data) => [
                            'contactList' => fn() => $contactList->load('fields'),
                            'contacts' => fn() => ContactResource::collection($data()),
                        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreContactRequest $request, ContactList $contactList): RedirectResponse
    {
        try {
            $contactList->addContact($request->validated());
        } catch (Throwable) {
            return Redirect::back()->with('error', __('messages.global.error'));
        }

        return Redirect::back()->with('success', __('messages.contact.created'));
    }

    /**
     * Show the form for importing new resources.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function import(ContactList $contactList): Response
    {
        Gate::authorize('update', $contactList);

        return Inertia::render('ContactLists/Import', [
            'maxUploadSize' => fn() => Environment::getUploadMaxSize(),
            'contactList' => fn() => $contactList->load('fields'),
            'importStatus' => fn() => Monitor::for(ContactsImport::class, $contactList->id)
        ]);
    }

    /**
     * Import resources to storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function dispatchImport(ImportContactsData $data, ContactList $contactList): RedirectResponse
    {
        Gate::authorize('create', Contact::class);

        (new ContactsImport($contactList))->queue($data->file);

        return Redirect::back()->with(['success' => __('messages.contact.import.queued')]);
    }

    /**
     * Cancel the import job.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function cancelImport(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('update', $contactList);

        $monitor = Monitor::for(ContactsImport::class, $contactList->id);
        if ($monitor->cancel()) {
            return Redirect::back()->with(['success' => __('messages.contact.import.cancelled')]);
        } else {
            return Redirect::back()->with(['error' => __('messages.contact.import.not_running')]);
        }
    }

    /**
     * Clear cache associated with the import job.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function clearImport(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('update', $contactList);

        $monitor = Monitor::for(ContactsImport::class, $contactList->id);
        if ($monitor->isFinished()) {
            $monitor->clear();
        }

        return Redirect::back();
    }

    /**
     * Download log file created by import job.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadImportLog(ContactList $contactList): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('update', $contactList);

        $monitor = Monitor::for(ContactsImport::class, $contactList->id);
        if ($monitor->logfile && Storage::exists($monitor->logfile)) {
            return response()->download(Storage::path($monitor->logfile), "contact-list-{$contactList->id}-import.log");
        }
        return Redirect::back();
    }

    /**
     * Show the form for exporting resources.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function export(ContactList $contactList): Response
    {
        Gate::authorize('export', $contactList);

        return Inertia::render('ContactLists/Export', [
            'contactList' => fn() => $contactList,
            'exportStatus' => fn() => Monitor::for(ContactsExport::class, $contactList->id)
        ]);
    }

    /**
     * Export resources to excel.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function dispatchExport(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('export', $contactList);

        (new ContactsExport())->forContactList($contactList)
                              ->queue($contactList->export_path)
                              ->chain([
                                  function () use ($contactList) {
                                      $monitor = Monitor::for(ContactsExport::class, $contactList->id);
                                      $monitor->status = 'Completed';
                                  }
                              ]);

        return Redirect::back()->with('success', __('messages.contact.export.queued'));
    }

    /**
     * Clear cache associated with the export job.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function clearExport(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('export', $contactList);

        $monitor = Monitor::for(ContactsExport::class, $contactList->id);
        if ($monitor->processed === $monitor->total) {
            $monitor->clear();
            File::delete($contactList->export_path);
        }

        return Redirect::back();
    }

    /**
     * Remove the specified resources from storage.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delete(ContactList $contactList): RedirectResponse
    {
        Gate::authorize('update', $contactList);

        $count = DataTable::make($contactList->contacts())
                          ->search(fn($query, $search) => $query->search($search))
                          ->query()
                          ->delete();

        if ($count > 0) {
            return Redirect::back()->with('success', trans_choice('messages.contact.delete.success', $count));
        }

        return Redirect::back()->with('error', __('messages.contact.delete.failed'));
    }

    /**
     * Download exported file.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadExport(ContactList $contactList): BinaryFileResponse|RedirectResponse
    {
        Gate::authorize('export', $contactList);

        if (Storage::exists($contactList->export_path)) {
            return response()->download(Storage::path($contactList->export_path), "contact-list-{$contactList->id}.xlsx");
        }

        return Redirect::back()->with('error', __('messages.global.error'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateContactRequest $request, Contact $contact): RedirectResponse
    {
        try {
            $contact->modify($request->validated());
        } catch (Throwable) {
            return Redirect::back()->with('error', __('messages.global.error'));
        }

        return Redirect::back()->with('success', __('messages.contact.updated'));
    }

    public function unsubscribe(ContactList $contactList, UnsubscribeContactData $data): Response
    {
        $contactList->contacts()
                    ->whereMobileNumber($data->mobileNumber)
                    ->update([
                        'subscribed' => false
                    ]);

        return Inertia::render('ContactLists/Unsubscribe');
    }

    /**
     * Download a sample CSV file for a contact list.
     *
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function downloadImportSample(ContactList $contactList): BinaryFileResponse
    {
        Gate::authorize('view', $contactList);

        // Create a temporary file
        $tempFile = tempnam(sys_get_temp_dir(), 'sample_');
        $file = fopen($tempFile, 'w');

        // Add headers
        $headers = ['mobile_number'];
        foreach ($contactList->fields as $field) {
            $headers[] = $field->tag;
        }
        fputcsv($file, $headers);

        // Add sample rows
        for ($i = 0; $i < 5; $i++) {
            $row = ['+' . rand(1, 99) . rand(1000000000, 9999999999)];
            foreach ($contactList->fields as $field) {
                $row[] = match ($field->type) {
                    FieldType::Text => Str::random(8),
                    FieldType::Email => Str::random(5) . '@example.com',
                    FieldType::Textarea => implode(PHP_EOL, array_map(fn() => Str::random(20), range(1, rand(2, 5)))),
                    FieldType::Number => rand(10000, 99999),
                    FieldType::Date => date('Y-m-d', rand(strtotime('-2 years'), time())),
                    FieldType::DatetimeLocal => date('Y-m-d\TH:i', rand(strtotime('-2 years'), time())),
                    FieldType::Time => date('H:i', rand(0, 86399)),
                    FieldType::Dropdown, FieldType::Radio => data_get(Arr::random($field->options), 'value'),
                    FieldType::Multiselect, FieldType::Checkbox => implode(', ', Arr::pluck(Arr::random($field->options, rand(1, count($field->options) - 1)), 'value')),
                };
            }
            fputcsv($file, $row);
        }

        fclose($file);

        // Return the file as a download
        return response()->download($tempFile, "contact-list-{$contactList->id}-sample.csv", [
            'Content-Type' => 'text/csv',
        ])->deleteFileAfterSend();
    }
}
