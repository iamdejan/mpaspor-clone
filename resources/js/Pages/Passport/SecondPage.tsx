import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { JSX } from 'react';

type AdministrativeData = {
    code: string;
    name: string;
};

type Props = {
    workflow_id: string;
    provinces: AdministrativeData[];
};

type FormProps = {
    street_address: string;
    rt: string;
    rw: string;
    sub_district_code: string;
    district_code: string;
    city_code: string;
    province_code: string;
};

export default function FirstPage(props: Props): JSX.Element {
    const { data, setData, processing, errors } = useForm<FormProps>({
        street_address: '',
        rt: '',
        rw: '',
        sub_district_code: '',
        district_code: '',
        city_code: '',
        province_code: '',
    });

    return (
        <AuthenticatedLayout
            header={
                <h2 className="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
                    Passport Application #{props.workflow_id} - Second Page
                </h2>
            }
        >
            <Head title="Second Page" />

            <div className="py-12">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">
                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg dark:bg-gray-800">
                        <div className="p-6 text-gray-900 dark:text-gray-100">
                            <form>
                                <h3>Domicile Address</h3>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="street_address"
                                        value="Street Address"
                                    />

                                    <TextInput
                                        type="text"
                                        id="street_address"
                                        name="street_address"
                                        value={data.street_address}
                                        className="mt-1 block w-full"
                                        autoComplete="username"
                                        isFocused={true}
                                        onChange={(e) =>
                                            setData(
                                                'street_address',
                                                e.target.value,
                                            )
                                        }
                                    />

                                    <InputError
                                        message={errors.street_address}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="province"
                                        value="Province"
                                    />

                                    <select
                                        id="province"
                                        name="province"
                                        className="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                    >
                                        {props.provinces.map((entry) => (
                                            <option
                                                key={entry.code}
                                                value={entry.code}
                                            >
                                                {entry.name}
                                            </option>
                                        ))}
                                    </select>
                                </div>

                                <div className="mt-6">
                                    <PrimaryButton
                                        disabled={processing}
                                        type="submit"
                                    >
                                        Submit
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
