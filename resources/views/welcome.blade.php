<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel</title>


    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.tailwindcss.com?plugins=forms,typography,aspect-ratio,line-clamp"></script>
</head>

<body class="font-sans antialiased">
    <form class="max-w-xl p-11 mx-auto" method="POST">
        @csrf
        <div class="space-y-12">
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Profile</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">
                    Post the war timeframe here.
                </p>
                <p class="bg-gray-200 p-2">
                    17:00:00 - 16/03/24 until 19:05:12 - 18/03/24
                </p>

                <div class="mt-10 flex">
                    <div class="w-full">
                        <label for="timeframe" class="block text-sm font-medium leading-6 text-gray-900">
                            Timeframe
                        </label>
                        <div class="mt-2">
                            <input type="text" name="timeframe" id="timeframe"
                                placeholder="17:00:00 - 16/03/24 until 19:05:12 - 18/03/24"class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <button type="button" class="text-sm font-semibold leading-6 text-gray-900">Cancel</button>
            <button type="submit"
                class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Save</button>
        </div>
    </form>

</body>

</html>
