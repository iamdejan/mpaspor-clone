import InputError from '@/Components/InputError';
import InputLabel from '@/Components/InputLabel';
import PrimaryButton from '@/Components/PrimaryButton';
import SecondaryButton from '@/Components/SecondaryButton';
import TextInput from '@/Components/TextInput';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useQuery } from '@tanstack/react-query';
import ky from 'ky';
import { FormEventHandler, JSX, useState } from 'react';

type AdministrativeData = {
    id: string;
    name: string;
};

type Props = {
    workflow_id: string;
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

export default function SecondPage(props: Props): JSX.Element {
    const { data, setData, post, processing, errors } = useForm<FormProps>({
        street_address: '',
        rt: '',
        rw: '',
        sub_district_code: '',
        district_code: '',
        city_code: '',
        province_code: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(
            route('passport.second-page.submit', {
                workflow_id: props.workflow_id,
            }),
        );
    };

    const provincesQuery = useQuery<AdministrativeData[]>({
        queryKey: ['provinces'],
        queryFn: async function (): Promise<AdministrativeData[]> {
            return await ky
                .get<
                    AdministrativeData[]
                >('https://www.emsifa.com/api-wilayah-indonesia/api/provinces.json')
                .json();
        },
    });
    const [province, setProvince] = useState<string>('');

    const citiesQuery = useQuery<AdministrativeData[]>({
        queryKey: ['provinces', province, 'cities'],
        queryFn: async function (): Promise<AdministrativeData[]> {
            return await ky
                .get<
                    AdministrativeData[]
                >(`https://www.emsifa.com/api-wilayah-indonesia/api/regencies/${province}.json`)
                .json();
        },
        enabled: province !== '',
    });
    const [city, setCity] = useState<string>('');

    const districtsQuery = useQuery<AdministrativeData[]>({
        queryKey: ['provinces', province, 'cities', city, 'districts'],
        queryFn: async function (): Promise<AdministrativeData[]> {
            return await ky
                .get<
                    AdministrativeData[]
                >(`https://www.emsifa.com/api-wilayah-indonesia/api/districts/${city}.json`)
                .json();
        },
        enabled: city !== '',
    });
    const [district, setDistrict] = useState<string>('');

    const subDistrictsQuery = useQuery<AdministrativeData[]>({
        queryKey: [
            'provinces',
            province,
            'cities',
            city,
            'districts',
            district,
            'sub-districts',
        ],
        queryFn: async function (): Promise<AdministrativeData[]> {
            return await ky
                .get<
                    AdministrativeData[]
                >(`https://www.emsifa.com/api-wilayah-indonesia/api/villages/${district}.json`)
                .json();
        },
        enabled: district !== '',
    });
    const [, setSubDistrict] = useState<string>('');

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
                            <form onSubmit={submit}>
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

                                <div className="mt-4 grid grid-cols-2">
                                    <div className="mr-2">
                                        <InputLabel htmlFor="rt" value="RT" />

                                        <TextInput
                                            type="text"
                                            id="rt"
                                            name="rt"
                                            value={data.rt}
                                            className="mt-1 block w-full"
                                            onChange={(e) =>
                                                setData('rt', e.target.value)
                                            }
                                        />

                                        <InputError
                                            message={errors.rt}
                                            className="mt-2"
                                        />
                                    </div>
                                    <div className="ml-2">
                                        <InputLabel htmlFor="rw" value="RW" />

                                        <TextInput
                                            type="text"
                                            id="rw"
                                            name="rw"
                                            value={data.rw}
                                            className="mt-1 block w-full"
                                            onChange={(e) =>
                                                setData('rw', e.target.value)
                                            }
                                        />

                                        <InputError
                                            message={errors.rw}
                                            className="mt-2"
                                        />
                                    </div>
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
                                        onChange={(e) => {
                                            setProvince(e.target.value);
                                            setData(
                                                'province_code',
                                                e.target.value,
                                            );
                                        }}
                                        disabled={provincesQuery.isLoading}
                                    >
                                        <option value=""></option>
                                        {provincesQuery.isSuccess ? (
                                            provincesQuery.data.map((entry) => (
                                                <option
                                                    key={entry.id}
                                                    value={entry.id}
                                                >
                                                    {entry.name}
                                                </option>
                                            ))
                                        ) : (
                                            <></>
                                        )}
                                    </select>

                                    <InputError
                                        message={errors.province_code}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel htmlFor="city" value="City" />

                                    <select
                                        id="city"
                                        name="city"
                                        className="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        onChange={(e) => {
                                            setCity(e.target.value);
                                            setData(
                                                'city_code',
                                                e.target.value,
                                            );
                                        }}
                                        disabled={citiesQuery.isLoading}
                                    >
                                        <option value=""></option>
                                        {citiesQuery.isSuccess ? (
                                            citiesQuery.data.map((entry) => (
                                                <option
                                                    key={entry.id}
                                                    value={entry.id}
                                                >
                                                    {entry.name}
                                                </option>
                                            ))
                                        ) : (
                                            <></>
                                        )}
                                    </select>

                                    <InputError
                                        message={errors.city_code}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="district"
                                        value="District"
                                    />

                                    <select
                                        id="district"
                                        name="district"
                                        className="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        onChange={(e) => {
                                            setDistrict(e.target.value);
                                            setData(
                                                'district_code',
                                                e.target.value,
                                            );
                                        }}
                                        disabled={districtsQuery.isLoading}
                                    >
                                        <option value=""></option>
                                        {districtsQuery.isSuccess ? (
                                            districtsQuery.data.map((entry) => (
                                                <option
                                                    key={entry.id}
                                                    value={entry.id}
                                                >
                                                    {entry.name}
                                                </option>
                                            ))
                                        ) : (
                                            <></>
                                        )}
                                    </select>

                                    <InputError
                                        message={errors.district_code}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-4">
                                    <InputLabel
                                        htmlFor="sub-district"
                                        value="Sub-District"
                                    />

                                    <select
                                        id="sub-district"
                                        name="sub-district"
                                        className="block w-full rounded-lg border border-gray-300 bg-gray-50 p-2.5 text-sm text-gray-900 focus:border-blue-500 focus:ring-blue-500 dark:border-gray-600 dark:bg-gray-700 dark:text-white dark:placeholder-gray-400 dark:focus:border-blue-500 dark:focus:ring-blue-500"
                                        onChange={(e) => {
                                            setSubDistrict(e.target.value);
                                            setData(
                                                'sub_district_code',
                                                e.target.value,
                                            );
                                        }}
                                        disabled={subDistrictsQuery.isLoading}
                                    >
                                        <option value=""></option>
                                        {subDistrictsQuery.isSuccess ? (
                                            subDistrictsQuery.data.map(
                                                (entry) => (
                                                    <option
                                                        key={entry.id}
                                                        value={entry.id}
                                                    >
                                                        {entry.name}
                                                    </option>
                                                ),
                                            )
                                        ) : (
                                            <></>
                                        )}
                                    </select>

                                    <InputError
                                        message={errors.sub_district_code}
                                        className="mt-2"
                                    />
                                </div>

                                <div className="mt-6 grid grid-cols-2">
                                    <div>
                                        <Link
                                            href={route(
                                                'passport.first-page.view',
                                                {
                                                    workflow_id:
                                                        props.workflow_id,
                                                },
                                            )}
                                        >
                                            <SecondaryButton
                                                disabled={processing}
                                                type="button"
                                            >
                                                Go Back
                                            </SecondaryButton>
                                        </Link>
                                    </div>
                                    <div className="ml-auto mr-0">
                                        <PrimaryButton
                                            disabled={processing}
                                            type="submit"
                                        >
                                            Submit
                                        </PrimaryButton>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
