import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { FormEventHandler, JSX } from 'react';

type Props = {
    workflow_id: string;
    identity_card_path: string | null;
    old_passport_path: string | null;
};

type FormProps = {
    identity_card: File | null;
    old_passport: File | null;

    // for error message only
    identity_card_path: string | null;
    old_passport_path: string | null;
};

export default function FirstPage(props: Props): JSX.Element {
    const { setData, post, processing, errors } = useForm<FormProps>({
        identity_card: null,
        old_passport: null,
        //
        identity_card_path: null,
        old_passport_path: null,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(
            route('passport.first-page.submit', {
                workflow_id: props.workflow_id,
            }),
        );
    };

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Passport Application #{props.workflow_id} - First Page
                </h2>
            }
        >
            <Head title="First Page" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form onSubmit={submit}>
                                <div>
                                    <InputLabel
                                        htmlFor="identity_card"
                                        value="Identity Card (KTP)"
                                    />

                                    {props.identity_card_path && (
                                        <img
                                            src={props.identity_card_path}
                                            width={60}
                                        />
                                    )}

                                    <input
                                        className="mt-1 block w-full"
                                        type="file"
                                        id="identity_card"
                                        name="identity_card"
                                        onChange={(e) => {
                                            if (e.target.files) {
                                                setData(
                                                    'identity_card',
                                                    e.target.files[0],
                                                );
                                            }
                                        }}
                                    />

                                    <InputError
                                        message={errors.identity_card_path}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-5">
                                    <InputLabel
                                        htmlFor="old_passport"
                                        value="Old Passport"
                                    />

                                    {props.old_passport_path && (
                                        <img
                                            src={props.old_passport_path}
                                            width={60}
                                        />
                                    )}

                                    <input
                                        className="mt-1 block w-full"
                                        type="file"
                                        id="old_passport"
                                        name="old_passport"
                                        onChange={(e) => {
                                            if (e.target.files) {
                                                setData(
                                                    'old_passport',
                                                    e.target.files[0],
                                                );
                                            }
                                        }}
                                    />

                                    <InputError
                                        message={errors.old_passport_path}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-6">
                                    <PrimaryButton
                                        disabled={processing}
                                        type="submit"
                                    >
                                        Next
                                    </PrimaryButton>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
